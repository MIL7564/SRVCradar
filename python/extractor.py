import comtypes.client

YOUR_PHONE_APP_GUID = "{A5C935F2-7306-4DBF-B24D-CFF38D6B0B68}"
MESSAGING_INTERFACE_GUID = "{1F72D5D8-B5C0-43A5-958C-892B17692343}"

def extract_text_messages():
    your_phone_app = comtypes.client.CreateObject(
        clsid=comtypes.GUID(YOUR_PHONE_APP_GUID),
        interface=comtypes.gen.Microsof_YourPhone_1_0.IYourPhone
    )

    messaging_interface = your_phone_app.OpenMessaging()
    text_messages = messaging_interface.GetMessages()

    with open("text_messages.txt", "w") as f:
        for message in text_messages:
            citizen_initial = message.body[0].upper()
            legion_number = resolve_citizen(citizen_initial)
            if legion_number is not None:
                f.write(f"Initial of Citizen (in need): {citizen_initial}\n")
                f.write(f"Legion number of the Citizen: {legion_number}\n")
                f.write(f"Mobilephone number of Rescuer: {message.sender}\n")
                f.write("\n")

