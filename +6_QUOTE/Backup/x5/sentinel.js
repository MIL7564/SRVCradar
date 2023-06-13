// JavaScript equivalent of resolute function
function resolute(phNum) {
    let digits = Array.from(String(phNum), Number);
    while (digits.length > 1) {
      digits = Array.from(String(digits.reduce((a, b) => a + b)), Number);
    }
    return digits[0];
  }
  
  class Legion {
    constructor(name, score) {
      this.name = name;
      this.score = score;
    }
  }
  
  class SentinelApp {
    constructor() {
      this.cac = "";
      this.act = false;
      this.db = null;
      this.results = null;
      this.winningLegions = [];
      this.initialize();
    }
  
    initialize() {
      this.winningLegions = [];
      this.openDatabaseConnection();
      this.getLegionScores();
    }
  
    openDatabaseConnection() {
      // No need to connect to SQLite database in JavaScript
    }
  
    getLegionScores() {
      const legionNames = [
        "Legion 1",
        "Legion 2",
        "Legion 3",
        "Legion 4",
        "Legion 5",
        "Legion 6",
        "Legion 7",
        "Legion 8",
        "Legion 9",
      ];
      this.results = [];
  
      for (const legionName of legionNames) {
        const score = 0; // Set initial score to 0 in JavaScript
        this.results.push(new Legion(legionName, score));
      }
    }
  
    updateLegionScore(legionName, score) {
      // No need to update SQLite database in JavaScript
    }
  
    handleCACInput(input) {
      this.cac = input.trim();
    }
  
    handleAOKInput(input) {
      this.act = input.trim().toLowerCase() === "yes";
    }
  
    submitForm() {
      const userLegion = resolute(this.cac);
      const currentScore = this.results[userLegion - 1].score;
      const newScore = this.act ? currentScore + 1 : currentScore;
      this.results[userLegion - 1].score = newScore;
      this.getLegionScores();
  
      // Update the displayed scores
      const scoreLabel = document.getElementById("score-label");
      scoreLabel.innerText = this.getLegionScoresText();
  
      this.determineWinningLegions();
  
      // Clear the input fields
      const cacEntry = document.getElementById("cac-input");
      const aokEntry = document.getElementById("aok-input");
      cacEntry.value = "";
      aokEntry.value = "";
  
      // Update the displayed scores
      scoreLabel.innerText = this.getLegionScoresText();
    }
  
    determineWinningLegions() {
      const highestScore = Math.max(...this.results.map((legion) => legion.score));
      this.winningLegions = this.results.filter((legion) => legion.score === highestScore);
    }
  
    getLegionScoresText() {
      let scoresText = "Legion Scores:\n";
      for (const legion of this.results) {
        scoresText += `${legion.name}: ${legion.score}\n`;
      }
      return scoresText;
    }
  
    run() {
      // Function to handle the submit button click event
      const submitForm = () => {
        const cacInput = document.getElementById("cac-input");
        const aokInput = document.getElementById("aok-input");
        this.handleCACInput(cacInput.value);
        this.handleAOKInput(aokInput.value);
        this.submitForm();
      };
  
      // Add event listener to submit button
      const submitButton = document.getElementById("submit-button");
      submitButton.addEventListener("click", submitForm);
  
      // Create and position the score display label
      const scoreLabel = document.createElement("div");
      scoreLabel.id = "score-label";
      scoreLabel.textContent = this.getLegionScoresText();
      document.body.appendChild(scoreLabel);
  
      // Run the JavaScript code
      this.getLegionScores();
    }
  }
  
  // Create an instance of the SentinelApp class and run it
  const app = new SentinelApp();
  app.run();
  