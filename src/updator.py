import time

def update_text_messages():
    while True:
        text_messages = get_text_messages()
        with open("text_messages.txt", "w") as f:
            for message in text_messages:
                f.write(f"{message['initial']},{message['legion']},{message['rescuer_number']}\n")
        time.sleep(60)
