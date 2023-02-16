import time
from clientee import download_file

def repeat_download():
    while True:
        download_file()
        time.sleep(60)
