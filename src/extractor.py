import winrt.windows.applicationmodel as appmodel

# Get the Your Phone app
app = appmodel.AppServiceConnection()
app.app_service_name = "Microsoft.YourPhone_8wekyb3d8bbwe!App"
app.package_family_name = "Microsoft.YourPhone_8wekyb3d8bbwe"

# Open the app and get the text messages
result = app.open_async()
text_messages = result.get()

# Save the text messages to a file
with open("text_messages.txt", "w") as f:
    for message in text_messages:
        f.write(f"From: {message.sender}\n")
        f.write(f"To: {message.recipient}\n")
        f.write(f"Body: {message.body}\n")
        f.write(f"Timestamp: {message.timestamp}\n")
        f.write("\n")
