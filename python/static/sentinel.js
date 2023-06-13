// JavaScript equivalent of resolute function
function resolute(phNum) {
  let digits = Array.from(String(phNum), Number);
  while (digits.length > 1) {
    digits = Array.from(String(digits.reduce((a, b) => a + b)), Number);
  }
  return digits[0];
}

class SentinelApp {
  constructor() {
    this.cac = "";
    this.act = false;
    this.results = null;
    this.winningLegions = [];
    this.initialize();
  }

  initialize() {
    this.winningLegions = [];
    this.getLegionScores();
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
      this.results.push({ name: legionName, score: score });
    }
  }

  handleCACInput(input) {
    this.cac = input.trim();
  }

  handleAOKInput(input) {
    this.act = input.trim().toLowerCase() === "yes";
  }

  submitForm() {
    const cacInput = document.getElementById("cac-input");
    const aokInput = document.getElementById("aok-input");
    this.handleCACInput(cacInput.value);
    this.handleAOKInput(aokInput.value);

    // Perform the necessary calculations and update the database using Flask's endpoint
    const xhr = new XMLHttpRequest();
    const url = "http://localhost:5000/submit";
    const params = `cac=${encodeURIComponent(this.cac)}&aok=${encodeURIComponent(this.act ? 'yes' : 'no')}`;
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(params);

    // Clear the input fields
    cacInput.value = "";
    aokInput.value = "";
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
