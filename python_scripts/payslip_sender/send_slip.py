
import os
import re
import datetime
import logging
import pdfplumber
import mimetypes
import base64
import argparse
import sys
import config
from email.message import EmailMessage
from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from google_auth_oauthlib.flow import InstalledAppFlow
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError
from cryptography.fernet import Fernet
import json

# =================================================================================================
# CONFIGURATION
# =================================================================================================

# Use config values, but allow override via args
# Base directory
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
PROJECT_ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

# Paths (Default from config, can be overridden)
CREDENTIALS_DIR = config.CREDENTIALS_DIR
CREDENTIALS_FILE = config.CREDENTIALS_FILE
TOKEN_FILE = config.TOKEN_FILE
LOG_DIR = config.LOG_DIR
KEY_FILE = config.KEY_FILE

# Email Regex Pattern
EMAIL_REGEX = config.EMAIL_REGEX

# Scopes
SCOPES = ["https://www.googleapis.com/auth/gmail.send"]

# =================================================================================================
# SECURITY MODULE
# =================================================================================================

def generate_key():
    """Generates a key and saves it into a file if not exists."""
    if not os.path.exists(KEY_FILE):
        key = Fernet.generate_key()
        with open(KEY_FILE, "wb") as key_file:
            key_file.write(key)
        # print(f"Key generated and saved to {KEY_FILE}")

def load_key():
    """Loads the key from the directory."""
    if not os.path.exists(KEY_FILE):
        generate_key()
    return open(KEY_FILE, "rb").read()

def decrypt_file(file_path):
    """Decrypts a file and returns the data."""
    if not os.path.exists(file_path):
        # print(f"Encrypted file {file_path} not found.")
        return None

    key = load_key()
    f = Fernet(key)

    with open(file_path, "rb") as file:
        encrypted_data = file.read()

    try:
        decrypted_data = f.decrypt(encrypted_data)
        return decrypted_data
    except Exception as e:
        # print(f"Decryption failed: {e}")
        return None

def encrypt_file(file_path):
    """Encrypts a file."""
    if not os.path.exists(file_path):
        # print(f"File {file_path} not found.")
        return False

    key = load_key()
    f = Fernet(key)

    with open(file_path, "rb") as file:
        file_data = file.read()

    encrypted_data = f.encrypt(file_data)

    with open(file_path + ".enc", "wb") as file:
        file.write(encrypted_data)
    
    # print(f"File encrypted: {file_path}.enc")
    return True

# =================================================================================================
# LOGGING SETUP
# =================================================================================================

def setup_logging():
    if not os.path.exists(LOG_DIR):
        os.makedirs(LOG_DIR)
    
    timestamp = datetime.datetime.now().strftime('%Y%m%d_%H%M%S')
    log_file = os.path.join(LOG_DIR, f"payslip_log_{timestamp}.log")
    
    logging.basicConfig(
        level=logging.INFO,
        format='%(asctime)s - %(levelname)s - %(message)s',
        handlers=[
            logging.FileHandler(log_file),
            # logging.StreamHandler() # Disable stream handler to not pollute JSON output
        ]
    )
    return log_file

# =================================================================================================
# GMAIL SERVICE
# =================================================================================================

def get_gmail_service():
    creds = None
    
    # Check if token.json exists
    if os.path.exists(TOKEN_FILE):
        creds = Credentials.from_authorized_user_file(TOKEN_FILE, SCOPES)
    
    # If no valid credentials available
    if not creds or not creds.valid:
        if creds and creds.expired and creds.refresh_token:
            try:
                creds.refresh(Request())
            except Exception as e:
                logging.error(f"Error refreshing token: {e}")
                creds = None

        if not creds:
            # Check for credentials.json or encrypted version
            client_config = None
            
            # Prefer encrypted
            if os.path.exists(CREDENTIALS_FILE + ".enc"):
                logging.info("Using encrypted credentials...")
                decrypted_data = decrypt_file(CREDENTIALS_FILE + ".enc")
                if decrypted_data:
                    client_config = json.loads(decrypted_data)
            
            # Fallback to plain json if exists
            elif os.path.exists(CREDENTIALS_FILE):
                logging.info("Using plain credentials.json...")
                # Auto-encrypt for next time
                encrypt_file(CREDENTIALS_FILE)
                with open(CREDENTIALS_FILE, 'r') as f:
                    client_config = json.load(f)
            
            if client_config:
                try:
                    flow = InstalledAppFlow.from_client_config(client_config, SCOPES)
                    creds = flow.run_local_server(port=0)
                except Exception as e:
                    logging.error(f"Error during OAuth flow: {e}")
                    return None
            else:
                logging.error(f"Credentials file not found at {CREDENTIALS_FILE} (or .enc version)")
                return None
        
        # Save the credentials for the next run
        with open(TOKEN_FILE, "w") as token:
            token.write(creds.to_json())

    try:
        service = build("gmail", "v1", credentials=creds)
        return service
    except HttpError as error:
        logging.error(f"An error occurred building service: {error}")
        return None

# =================================================================================================
# PDF PROCESSING
# =================================================================================================

def extract_email_from_pdf(pdf_path):
    try:
        text = ""
        with pdfplumber.open(pdf_path) as pdf:
            for page in pdf.pages:
                extracted = page.extract_text()
                if extracted:
                    text += extracted + " "
        
        # Search for email
        emails = re.findall(EMAIL_REGEX, text)
        
        # Try to extract name
        # Assuming format often involves "Nama : [Name]" or similar, but without specific pattern,
        # we might need to rely on filename if it contains the name.
        # Example filename: "1_IRGI IMANUNBIL MAJID.pdf"
        
        filename = os.path.basename(pdf_path)
        # Remove extension
        name_part = os.path.splitext(filename)[0]
        # Remove potential ID prefix like "1_" or "123 "
        # Regex to remove leading digits and symbols
        name = re.sub(r'^[\d\W_]+', '', name_part).strip()
        
        # Fallback to "Karyawan" if name extraction fails or is empty
        if not name:
            name = "Karyawan"
            
        if emails:
            # Return unique emails found, assuming the first one is the target
            # or filter based on domain if needed
            return emails[0], name
        return None, name
    except Exception as e:
        logging.error(f"Error reading PDF {pdf_path}: {e}")
        return None, None

# =================================================================================================
# EMAIL SENDING
# =================================================================================================

def create_message_with_attachment(sender, to, subject, message_text, file_path):
    message = EmailMessage()
    message.set_content(message_text)
    message["To"] = to
    message["From"] = sender
    message["Subject"] = subject

    # Attachment
    ctype, encoding = mimetypes.guess_type(file_path)
    if ctype is None or encoding is not None:
        ctype = "application/octet-stream"
    
    maintype, subtype = ctype.split("/", 1)
    
    with open(file_path, "rb") as fp:
        file_data = fp.read()
        filename = os.path.basename(file_path)
        
    message.add_attachment(
        file_data,
        maintype=maintype,
        subtype=subtype,
        filename=filename
    )

    return {"raw": base64.urlsafe_b64encode(message.as_bytes()).decode()}

def send_message(service, user_id, message):
    try:
        sent_message = service.users().messages().send(userId=user_id, body=message).execute()
        logging.info(f"Message Id: {sent_message['id']} Sent.")
        return sent_message
    except HttpError as error:
        logging.error(f"An error occurred sending message: {error}")
        return None

# =================================================================================================
# MAIN EXECUTION
# =================================================================================================

def main():
    parser = argparse.ArgumentParser(description='Payslip Sender Script')
    parser.add_argument('--mode', choices=['preview', 'send'], default='preview', help='Mode: preview or send')
    parser.add_argument('--dir', type=str, default=config.PDF_FOLDER, help='Directory containing PDF files')
    parser.add_argument('--files', type=str, help='JSON string or path to JSON file containing list of files to send')
    
    args = parser.parse_args()
    
    log_file = setup_logging()
    logging.info(f"Starting Payslip Sender System in {args.mode.upper()} mode...")
    
    target_dir = args.dir
    
    # Ensure PDF directory exists
    if not os.path.exists(target_dir):
        if args.mode == 'preview':
             # Return JSON error
             print(json.dumps({"error": f"Directory not found: {target_dir}"}))
             return
        else:
            logging.error(f"Directory not found: {target_dir}")
            return

    # Scan PDFs
    pdf_files = []
    for root, dirs, files in os.walk(target_dir):
        for file in files:
            if file.lower().endswith('.pdf'):
                pdf_files.append(os.path.join(root, file))
    
    if not pdf_files:
        if args.mode == 'preview':
             print(json.dumps([])) # Empty list
             return
        else:
            logging.warning("No PDF files found.")
            return

    # PREVIEW MODE
    if args.mode == 'preview':
        results = []
        for file_path in pdf_files:
            filename = os.path.basename(file_path)
            email, name = extract_email_from_pdf(file_path)
            
            results.append({
                "filename": filename,
                "filepath": file_path, # internal path
                "email": email if email else "",
                "name": name,
                "status": "valid" if email else "invalid",
                "message": "Email found" if email else "Email not found"
            })
        
        # Output JSON to stdout
        print(json.dumps(results))
        return

    # SEND MODE
    elif args.mode == 'send':
        # Initialize Gmail Service
        service = get_gmail_service()
        if not service:
            logging.error("Failed to initialize Gmail service. Exiting.")
            print(json.dumps({"error": "Failed to initialize Gmail service"}))
            return

        # Get Sender Profile
        sender_email = "Finance Department" # Default fallback
        try:
            profile = service.users().getProfile(userId='me').execute()
            sender_email = profile['emailAddress']
            logging.info(f"Authenticated as: {sender_email}")
        except Exception as e:
            logging.warning(f"Could not get profile (likely scope issue): {e}. Proceeding with default sender.")
            # Do not return, continue with fallback


        current_month = datetime.datetime.now().strftime("%B %Y")
        
        # Filter files if --files argument is provided
        files_to_send = pdf_files # Default all
        if args.files:
            try:
                # Try parsing as JSON string
                try:
                    selected_files = json.loads(args.files)
                except json.JSONDecodeError:
                    # Try reading from file
                    if os.path.exists(args.files):
                        with open(args.files, 'r') as f:
                            selected_files = json.load(f)
                    else:
                        selected_files = []
                
                # If we have a list of filenames, filter pdf_files
                # Assume selected_files is a list of filenames
                if selected_files:
                    files_to_send = [f for f in pdf_files if os.path.basename(f) in selected_files]
            except Exception as e:
                logging.error(f"Error parsing files argument: {e}")
                # Fallback to all files
        
        success_count = 0
        fail_count = 0
        results = []

        for file_path in files_to_send:
            filename = os.path.basename(file_path)
            logging.info(f"Processing file: {filename}")
            
            email, name = extract_email_from_pdf(file_path)
            
            if email:
                subject = config.EMAIL_SUBJECT_TEMPLATE.format(month=current_month)
                body = config.EMAIL_BODY_TEMPLATE.format(month=current_month, name=name)
                
                message = create_message_with_attachment(sender_email, email, subject, body, file_path)
                result = send_message(service, "me", message)
                
                if result:
                    logging.info(f"Status: BERHASIL - {filename} -> {email}")
                    success_count += 1
                    results.append({"filename": filename, "status": "sent", "email": email})
                else:
                    logging.error(f"Status: GAGAL SEND - {filename} -> {email}")
                    fail_count += 1
                    results.append({"filename": filename, "status": "failed", "email": email})
            else:
                logging.warning(f"Status: GAGAL - Email TIDAK DITEMUKAN in {filename}")
                fail_count += 1
                results.append({"filename": filename, "status": "skipped", "message": "No email found"})
        
        # Output summary
        print(json.dumps({
            "status": "completed",
            "success": success_count,
            "failed": fail_count,
            "details": results
        }))

if __name__ == "__main__":
    main()
