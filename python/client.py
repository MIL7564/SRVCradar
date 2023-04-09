import time
from ftplib import FTP

# Step 6: download updated text_messages.txt from FTP server
while True:
    with open('text_messages.txt', 'wb') as f:
        ftp = FTP('example.com')
        ftp.login(user='username', passwd='password')
        ftp.retrbinary('RETR text_messages.txt', f.write)
    time.sleep(60)

# Step 7: download text_messages.txt every 60 seconds
while True:
    with open('text_messages.txt', 'r') as f:
        messages = f.readlines()
    for message in messages:
        # Step 8: interpret text messages and display
        initial, phone_number = message.split(',')
        legion = simple_ams_dict.get(initial, 'Unknown')
        print(f'Number {legion} Legion! Our citizen {initial} requires help. For details text/call: rescuer at {phone_number}')
    time.sleep(60)
