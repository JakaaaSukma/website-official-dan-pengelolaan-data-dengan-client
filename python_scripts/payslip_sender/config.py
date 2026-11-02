# Config Settings
import os

# Base directory
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
PROJECT_ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

# Paths
CREDENTIALS_DIR = os.path.join(PROJECT_ROOT, 'database', 'gmail')
CREDENTIALS_FILE = os.path.join(CREDENTIALS_DIR, 'credentials.json')
TOKEN_FILE = os.path.join(CREDENTIALS_DIR, 'token.json')
PDF_FOLDER = os.path.join(PROJECT_ROOT, 'storage', 'app', 'public', 'payslips')
LOG_DIR = os.path.join(BASE_DIR, 'logs')

# Email Regex
EMAIL_REGEX = r'\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b'

# Email Settings
EMAIL_SUBJECT_TEMPLATE = "Slip Gaji - {month}"
EMAIL_BODY_TEMPLATE = """Yth. {name},

Berikut kami lampirkan slip gaji Anda untuk bulan {month}.
Mohon diperiksa kembali.

Terima kasih,
Finance Department
"""

# Security
# This key should be stored in .env or environment variable in production
# For this example, we generate/load it.
KEY_FILE = os.path.join(CREDENTIALS_DIR, 'secret.key')
