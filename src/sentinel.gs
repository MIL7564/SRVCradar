function updateSheet() {
  var spreadsheetName = "1UNSDG_FE"; // Change to the name of your spreadsheet
  var sheetName = "Sheet1"; // Change to the name of your sheet
  var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = spreadsheet.getSheetByName(sheetName);
  var range = sheet.getDataRange().getValues();
  
  // Create a new sheet called "Results"
  var newSheet = spreadsheet.insertSheet("Results");
  
  for (var i = 0; i < range.length; i++) {
    var firstLetter = range[i][1].charAt(0).toLowerCase();
    var digit = resolute(firstLetter);
    
    // Set the computed digit in the second column of the new sheet
    newSheet.getRange(i+1, 2).setValue(digit);
  }
}

function resolute(name) {
  var initial_value = name.charCodeAt(0) - 96;
  while (initial_value > 9) {
    var digits = Array.from(String(initial_value), Number);
    initial_value = digits.reduce(function(a, b) { return a + b; });
  }
  return initial_value;
}
