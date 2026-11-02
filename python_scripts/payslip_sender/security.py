from cryptography.fernet import Fernet
import os
import json

def generate_key(key_path):
    """Generates a key and saves it into a file."""
    if not os.path.exists(key_path):
        key = Fernet.generate_key()
        with open(key_path, "wb") as key_file:
            key_file.write(key)
        print(f"Key generated and saved to {key_path}")

def load_key(key_path):
    """Loads the key from the current directory named `secret.key`"""
    if not os.path.exists(key_path):
        generate_key(key_path)
    return open(key_path, "rb").read()

def encrypt_file(file_path, key_path):
    """Encrypts a file."""
    if not os.path.exists(file_path):
        print(f"File {file_path} not found.")
        return

    key = load_key(key_path)
    f = Fernet(key)

    with open(file_path, "rb") as file:
        file_data = file.read()

    encrypted_data = f.encrypt(file_data)

    with open(file_path + ".enc", "wb") as file:
        file.write(encrypted_data)
    
    print(f"File encrypted: {file_path}.enc")

def decrypt_file(file_path, key_path):
    """Decrypts a file and returns the data."""
    if not os.path.exists(file_path):
        print(f"File {file_path} not found.")
        return None

    key = load_key(key_path)
    f = Fernet(key)

    with open(file_path, "rb") as file:
        encrypted_data = file.read()

    decrypted_data = f.decrypt(encrypted_data)
    return decrypted_data
