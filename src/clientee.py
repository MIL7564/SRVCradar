import ftplib

def download_file():
    server = "ftp.example.com"
    username = "ftp_user"
    password = "ftp_password"
    ftp = ftplib.FTP(server)
    ftp.login(username, password)
    ftp.cwd("/")
    with open("text_messages.txt", "wb") as f:
        ftp.retrbinary("RETR text_messages.txt", f.write)
    ftp.quit()

download_file()
