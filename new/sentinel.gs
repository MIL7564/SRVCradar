function updateSheet() {
  var spreadsheetName = "9LEGIONS"; // Change to the name of your spreadsheet
  var sheetName = "Messages"; // Change to the name of your sheet
  var resultsSheetName = "Results"; // Change to the name of your results sheet
  var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = spreadsheet.getSheetByName(sheetName);
  var range = sheet.getDataRange().getValues();
  
  // set column A width to 200 Pixels
  sheet.setColumnWidth(1, 200);

  // Clear the sheet if there are 33 or more entries
  if (range.length > 32) {
    sheet.clearContent();
  }
  
  // Set the column labels for the "Results" sheet
  var resultsSheet = spreadsheet.getSheetByName(resultsSheetName);
  resultsSheet.getRange(1, 1).setValue("Legion Scores");
  resultsSheet.getRange(1,1).setFontWeight("bold");
  for (var i = 0; i < 9; i++) {
    resultsSheet.getRange(i+2, 1).setValue("Legion " + (i+1));
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

  // Determine the highest score and set the background color of the corresponding cell(s) to Yellow
  var maxScore = Math.max(...legionScores);
  for (var i = 0; i < 9; i++) {
    if (legionScores[i] === maxScore) {
      resultsSheet.getRange(i+2, 1, 1, 2).setBackground("yellow");
    }
    resultsSheet.getRange(i+2, 2).setValue(legionScores[i]);
  }
}

function resolute(name) {
  var charCode = name.charCodeAt(0);
  if (charCode < 97 || charCode > 122) {
    // Return 9 if the character is not one of the 26 letters of the English alphabet
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
