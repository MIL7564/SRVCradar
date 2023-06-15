import requests
import tkinter as tk

# Function to calculate Legion Number based on CAC
def resolute(phNum):
    digits = [int(char) for char in str(phNum) if char.isdigit()]
    if not digits:
        return 0
    while len(digits) > 1:
        digits = [int(char) for char in str(sum(digits))]
    return digits[0]


class SentinelApp:
    def __init__(self):
        self.cac = ""
        self.act = False
        self.initialize()

    def initialize(self):
        # Create the GUI window
        root = tk.Tk()
        root.title("Sentinel App")

        # Function to handle the submit button click event
        def submit_form():
            cac = cac_entry.get()
            self.handleCACInput(cac)

            aok = aok_entry.get()
            self.handleAOKInput(aok)

            # Perform the necessary calculations and update the database using Flask's endpoint
            url = 'http://localhost:5000/submit'
            data = {'cac': self.cac, 'aok': 'yes' if self.act else 'no'}
            response = requests.post(url, data=data)

            # Clear the input fields
            cac_entry.delete(0, tk.END)
            aok_entry.delete(0, tk.END)

        # Create and position the input fields
        cac_label = tk.Label(root, text="Enter your City Area Code, e.g. 416 for Toronto:")
        cac_label.pack()
        cac_entry = tk.Entry(root)
        cac_entry.pack()

        aok_label = tk.Label(root, text='Report an Act Of Kindness by you via typing "yes":')
        aok_label.pack()
        aok_entry = tk.Entry(root)
        aok_entry.pack()

        # Create and position the submit button
        submit_button = tk.Button(root, text="Submit", command=submit_form)
        submit_button.pack()

        # Run the GUI main loop
        root.mainloop()

    def handleCACInput(self, input):
        self.cac = input.strip()

    def handleAOKInput(self, input):
        self.act = input.strip().lower() == "yes"

if __name__ == "__main__":
    app = SentinelApp()
