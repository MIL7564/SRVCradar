function updateSheet() {
  var spreadsheetName = "1UNSDG_FE"; // Change to the name of your spreadsheet
  var sheetName = "Sheet1"; // Change to the name of your sheet
  var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = spreadsheet.getSheetByName(sheetName);
  var range = sheet.getDataRange().getValues();
  
  // Check if a sheet called "Results" already exists
  var newSheet = spreadsheet.getSheetByName("Results");
  if (!newSheet) {
    // Create a new sheet called "Results" if it doesn't exist
    newSheet = spreadsheet.insertSheet("Results");
    
    // Set the column labels for the "Results" sheet
    var columnLabels = [];
    for (var i = 1; i <= 9; i++) {
      columnLabels.push("Legion " + i);
    }
    newSheet.getRange(1, 1, 1, 9).setValues([columnLabels]);
  } else {
    // Check if the column labels have already been set on the "Results" sheet
    var columnLabels = newSheet.getRange(1, 1, 1, 9).getValues()[0];
    if (columnLabels.join() !== "Legion 1,Legion 2,Legion 3,Legion 4,Legion 5,Legion 6,Legion 7,Legion 8,Legion 9") {
      // Set the column labels if they haven't already been set
      for (var i = 1; i <= 9; i++) {
        newSheet.getRange(1, i).setValue("Legion " + i);
      }
    }
  }
  
  // Set the computed digits in the second column of the "Results" sheet
  for (var i = 0; i < range.length; i++) {
    var firstLetter = range[i][1].charAt(0).toLowerCase();
    var digit = resolute(firstLetter);
    newSheet.getRange(i+2, digit).setValue(digit);
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
