import sqlite3
import tkinter as tk
from tkinter import messagebox

# Function to calculate Legion Number based on CAC
def resolute(phNum):
    digits = [int(char) for char in phNum]
    while len(digits) > 1:
        digits = [int(char) for char in str(sum(digits))]
    return digits[0]

class Legion:
    def __init__(self, name, score):
        self.name = name
        self.score = score

class SentinelApp:
    def __init__(self):
        self.cac = ""
        self.act = False
        self.db = None
        self.results = None
        self.winningLegions = []
        self.initialize()

    def initialize(self):
        self.winningLegions = []
        self.openDatabaseConnection()
        self.getLegionScores()

    def openDatabaseConnection(self):
        path = "legion_scores.db"
        self.db = sqlite3.connect(path)
        cursor = self.db.cursor()
        cursor.execute("CREATE TABLE IF NOT EXISTS scores (legion_name TEXT, score INTEGER)")
        self.db.commit()

    def getLegionScores(self):
        legionNames = [
            "Legion 1",
            "Legion 2",
            "Legion 3",
            "Legion 4",
            "Legion 5",
            "Legion 6",
            "Legion 7",
            "Legion 8",
            "Legion 9",
        ]
        cursor = self.db.cursor()
        self.results = []
        for legionName in legionNames:
            cursor.execute("SELECT score FROM scores WHERE legion_name = ?", (legionName,))
            result = cursor.fetchone()
            score = result[0] if result else 0
            legion = next((legion for legion in self.results if legion.name == legionName), None)
            if legion:
                legion.score = score  # Update existing Legion object with the fetched score
            else:
                self.results.append(Legion(legionName, score))  # Create new Legion object with the fetched score

    def updateLegionScore(self, legionName, score):
        cursor = self.db.cursor()
        cursor.execute("INSERT OR REPLACE INTO scores (legion_name, score) VALUES (?, ?)", (legionName, score))
        self.db.commit()

    def handleCACInput(self, input):
        self.cac = input.strip()

    def handleAOKInput(self, input):
        self.act = input.strip().lower() == "yes"

    def submitForm(self):
        userLegion = resolute(self.cac)
        currentScore = self.results[userLegion - 1].score
        newScore = currentScore + 1 if self.act else currentScore
        self.updateLegionScore(self.results[userLegion - 1].name, newScore)
        self.getLegionScores()

        # Update the displayed scores
        self.score_label.config(text=self.getLegionScoresText())

        self.determineWinningLegions()

        if len(self.winningLegions) == 1 and self.winningLegions[0].name == self.results[userLegion - 1].name:
            messagebox.showinfo("Congratulations!", f"Your Legion ({self.winningLegions[0].name}) is the winner today.\nYou can take the next day off from donating.")
        elif self.results[userLegion - 1] not in self.winningLegions:
            messagebox.showinfo("Apologies!", f"Your Legion may not have won today, please donate tomorrow.")

        # Clear the input fields
        self.cac_entry.delete(0, tk.END)
        self.aok_entry.delete(0, tk.END)

        # Update the displayed scores
        self.score_label.config(text=self.getLegionScoresText())

    def determineWinningLegions(self):
        highestScore = max(legion.score for legion in self.results)
        self.winningLegions = [legion for legion in self.results if legion.score == highestScore]

        if len(self.winningLegions) > 1:
            messagebox.showinfo("Congratulations!", f"Your Legion ({self.winningLegions[0].name}) is the winner today.\nYou can take the next day off from donating.")

    def getLegionScoresText(self):
        scores_text = "Legion Scores:\n"
        for legion in self.results:
            scores_text += f"{legion.name}: {legion.score}\n"
        return scores_text

    def run(self):
        # Create the GUI window
        root = tk.Tk()
        root.title("Sentinel App")

        # Function to handle the submit button click event
        def submit_form():
            cac = self.cac_entry.get()
            self.handleCACInput(cac)

            aok = self.aok_entry.get()
            self.handleAOKInput(aok)

            self.submitForm()

        # Create and position the input fields
        cac_label = tk.Label(root, text="Enter your City Area Code:")
        cac_label.pack()
        self.cac_entry = tk.Entry(root)
        self.cac_entry.pack()

        aok_label = tk.Label(root, text='Report an Act Of Kindness by you to Homeless via typing "yes":')
        aok_label.pack()
        self.aok_entry = tk.Entry(root)
        self.aok_entry.pack()

        # Create and position the submit button
        submit_button = tk.Button(root, text="Submit", command=submit_form)
        submit_button.pack()

        # Create and position the score display label
        self.score_label = tk.Label(root, text=self.getLegionScoresText())
        self.score_label.pack()

        # Run the GUI main loop
        root.mainloop()

if __name__ == "__main__":
    app = SentinelApp()
    app.run()
