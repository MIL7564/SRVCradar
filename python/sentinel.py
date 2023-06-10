import sqlite3
import sys
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
        self.winningLegion = None
        self.initialize()

    def initialize(self):
        self.winningLegion = Legion("", 0)
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
            self.results.append(Legion(legionName, score))

    def updateLegionScore(self, legionName, score):
        cursor = self.db.cursor()
        cursor.execute("INSERT OR REPLACE INTO scores(legion_name, score) VALUES(?, ?)", (legionName, score))
        self.db.commit()

    def handleCACInput(self, input):
        self.cac = input.strip()

    def handleAOKInput(self, input):
        self.act = input.strip().lower() == "yes"

    def submitForm(self):
        userLegion = resolute(self.cac)
        currentScore = self.getLegionScore(self.results[userLegion - 1].name)
        newScore = currentScore + 1 if self.act else currentScore
        self.updateLegionScore(self.results[userLegion - 1].name, newScore)
        self.getLegionScores()

    def getLegionScore(self, legionName):
        cursor = self.db.cursor()
        cursor.execute("SELECT score FROM scores WHERE legion_name = ?", (legionName,))
        result = cursor.fetchone()
        return result[0] if result else 0

    def determineWinningLegion(self):
        winningLegion = self.results[0]
        for legion in self.results:
            if legion.score > winningLegion.score:
                winningLegion = legion
        return winningLegion

    def run(self):
        # Create the GUI window
        root = tk.Tk()
        root.title("Sentinel App")

        # Function to handle the submit button click event
        def submit_form():
            cac = cac_entry.get()
            self.handleCACInput(cac)

            aok = aok_entry.get()
            self.handleAOKInput(aok)

            self.submitForm()
            winningLegion = self.determineWinningLegion()

            if winningLegion.name == self.results[resolute(self.cac) - 1].name:
                messagebox.showinfo("Congratulations!", f"Your Legion ({winningLegion.name}) is the winner this week.\nYou can take the next week off from donating to those homeless.")
            else:
                messagebox.showinfo("Legions that did not win", "Keep up the good work in supporting the homeless.")

            # Clear the input fields
            cac_entry.delete(0, tk.END)
            aok_entry.delete(0, tk.END)

            # Update the displayed scores
            score_label.config(text=getLegionScoresText())

        def getLegionScoresText():
            scores_text = "Legion Scores:\n"
            for legion in self.results:
                scores_text += f"{legion.name}: {legion.score}\n"
            return scores_text

        # Create and position the input fields
        cac_label = tk.Label(root, text="Enter your City Area Code (CAC):")
        cac_label.pack()
        cac_entry = tk.Entry(root)
        cac_entry.pack()

        aok_label = tk.Label(root, text='Report an Act Of Kindness by you to Homeless via typing "yes":')
        aok_label.pack()
        aok_entry = tk.Entry(root)
        aok_entry.pack()

        # Create and position the submit button
        submit_button = tk.Button(root, text="Submit", command=submit_form)
        submit_button.pack()

        # Create and position the score display label
        score_label = tk.Label(root, text=getLegionScoresText())
        score_label.pack()

        # Run the GUI main loop
        root.mainloop()

if __name__ == "__main__":
    app = SentinelApp()
    app.run()
