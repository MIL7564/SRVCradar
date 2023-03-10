function updateSheet() {
  var sheetName = "1UNSDG_FE"; // Change to the name of your sheet
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName(sheetName);
  var range = sheet.getRange("B:B");
  var values = range.getValues();
  
  for (var i = 0; i < values.length; i++) {
    var firstLetter = values[i][0].charAt(0).toLowerCase();
    var digit = resolute(firstLetter);
    range.getCell(i+1, 2).setValue(digit);
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