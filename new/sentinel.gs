function updateSheet() {
  var spreadsheetName = "9LEGIONS"; // Change to the name of your spreadsheet
  var sheetName = "Sheet1"; // Change to the name of your sheet
  var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = spreadsheet.getSheetByName(sheetName);
  var range = sheet.getDataRange().getValues();
  
  // Check if there are 33 entries under column B
  if (range.length > 32) {
    // Get the winner of the previous set of 33 texts
    var legionScores = Array(9).fill(0);
    for (var i = 0; i < range.length - 1; i++) {
      if (range[i][1].length > 0) {
        var firstLetter = range[i][1].charAt(0).toLowerCase();
        var digit = resolute(firstLetter);
        legionScores[digit - 1]++;
      }
    }
    var maxScore = Math.max(...legionScores);
    var winner = "Legion " + (legionScores.indexOf(maxScore) + 1);
    
    // Declare the winner in Cell K11
    sheet.getRange("K11").setValue(winner + " won the previous set of 33 texts");
    
    // Clear the sheet except for the Legion Scores column
    sheet.getRange("A1:H" + range.length).clearContent();
  }
  
  // Set the column labels for the "Sheet1" sheet
  sheet.getRange(13, 10).setValue("Legion Scores");
  for (var i = 0; i < 9; i++) {
    sheet.getRange(i+14, 10).setValue("Legion " + (i+1));
  }

  // Add the occurrence of the digit to the corresponding Legion's score
  var legionScores = Array(9).fill(0);
  for (var i = 0; i < range.length; i++) {
    if (range[i][1].length > 0) {
      var firstLetter = range[i][1].charAt(0).toLowerCase();
      var digit = resolute(firstLetter);

      // Add the occurrence of the digit to the corresponding Legion's score
      legionScores[digit - 1]++;
    }
  }

  // Set the scores for each Legion in columns J and K
  for (var i = 0; i < 9; i++) {
    sheet.getRange(i+14, 10).setValue("Legion " + (i+1));
    sheet.getRange(i+14, 11).setValue(legionScores[i]);
  }
}

function resolute(name) {
  var charCode = name.charCodeAt(0);
  if (charCode < 97 || charCode > 122) {
    // Return 9 if the character is not one of the 26 letters of the English alphabet; overusage of 9, as there is also a Legion 9 that will benefit, will automatically devalue it
    return 9;
  } else {
    var initial_value = charCode - 96;
    while (initial_value > 9) {
      var digits = Array.from(String(initial_value), Number);
      initial_value = digits.reduce(function(a, b) { return a + b; });
    }
    return initial_value;
  }
}
