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
    const url = "/legion-scores"; // Update the URL to the Flask endpoint that returns the scores
    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        this.results = data;
        this.updateLegionScores();
      })
      .catch((error) => {
        console.log("Error fetching Legion scores:", error);
      });
  }

  updateLegionScores() {
    const tableBody = document.querySelector("#legion-scores tbody");
    tableBody.innerHTML = ""; // Clear the table body before updating

    for (const legion of this.results) {
      const row = document.createElement("tr");
      const nameCell = document.createElement("td");
      const scoreCell = document.createElement("td");
      const dateCell = document.createElement("td");

      nameCell.textContent = legion.legion_name;
      scoreCell.textContent = legion.score;
      dateCell.textContent = legion.date;

      row.appendChild(nameCell);
      row.appendChild(scoreCell);
      row.appendChild(dateCell);

      tableBody.appendChild(row);
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

    // Update the Legion scores after submitting the form
    this.getLegionScores();
  }

  getLegionScoresText() {
    let scoresText = "Legion Scores:\n";
    if (Array.isArray(this.results)) {
      for (const legion of this.results) {
        scoresText += `${legion.legion_name}: ${legion.score}\n`;
      }
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

    // Fetch and display the Legion scores
    this.getLegionScores();
  }
}

// Create an instance of the SentinelApp class and run it
const app = new SentinelApp();
app.run();
