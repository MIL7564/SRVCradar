import ftplib
import time

def upload_file():
    server = "ftp.example.com"
    username = "ftp_user"
    password = "ftp_password"
    ftp = ftplib.FTP(server)
    ftp.login(username, password)
    ftp.cwd("/")
    with open("text_messages.txt", "rb") as f:
        ftp.storbinary("STOR text_messages.txt", f)
    ftp.quit()

def update_ftp():
    while True:
        upload_file()
        time.sleep(60)
