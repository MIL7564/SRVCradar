var legionScores = Array(9).fill(0);

function onOpen() {
  setTrigger();
  autoexecute(); 
  updateSheet();  // Call updateSheet() when the spreadsheet is opened 
}

function autoexecute() {
  // The code to execute every 1 minutes goes here
  updateSheet();
  
  // Call the sort function to sort the "messages" sheet
  sortSheet();

  //add a cache-control header to your HTTP responses that instructs the client to not cache responses, and instead request fresh content from the server every time.
  doGet()
}  

function updateSheet() {
  var spreadsheetName = "9LEGIONS"; // Change to the name of your spreadsheet
  var messagesSheetName = "messages"; // Change to the name of your sheet
  var resultsSheetName = "results"; // Change to the name of your results sheet
  var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  var messagesSheet = spreadsheet.getSheetByName(messagesSheetName);
  var range = messagesSheet.getDataRange().getValues();
  var firstRow = messagesSheet.getRange(1, 1, 1, 2).getValues();

  // Set the column labels for the messages sheet if it's empty
  if (firstRow[0][0] == "" && firstRow[0][1] == "") {
  messagesSheet.getRange("A1").setValue("DATE").setFontWeight('bold').setHorizontalAlignment("left");
  messagesSheet.getRange("B1").setValue("PHONE").setFontWeight('bold').setHorizontalAlignment("left");
  messagesSheet.getRange("C1").setValue("MESSAGE").setFontWeight('bold').setHorizontalAlignment("left");

  // set column A width to 200 Pixels
  messagesSheet.setColumnWidth(1, 200);
  }
  
  // Clear the sheet if there are 14 or more entries (except the first row)
  if (range.length >= 15) {
  messagesSheet.getRange(2, 1, messagesSheet.getLastRow()-1, messagesSheet.getLastColumn()).clearContent();
  messagesSheet.getRange(1, 1, 1, 2).setFontWeight('bold').setHorizontalAlignment("left");
  messagesSheet.setColumnWidth(1, 200); 
  } 


  // Set the column labels for the "results" sheet
  var resultsSheet = spreadsheet.getSheetByName(resultsSheetName);
  resultsSheet.getRange("A1").setValue("LEGION").setFontWeight('bold');
  resultsSheet.getRange("B1").setValue("SCORE").setFontWeight('bold');
  // Set horizontal alignment for column A and B
  resultsSheet.getRange("A1:B1").setHorizontalAlignment("center");

  // Check if messages sheet is empty
  if (range.length > 1) {
    
  for (var i = 1; i < range.length; i++) {
    if (range[i][0].length > 0) {
      var phone = range[i][1];
      var digit = resolute(phone); // pass the phone number to the resolute function
      if (digit >= 1 && digit <= 9) {
        legionScores[digit - 1]++;
      }
    }
  } 


  }

  // Set legion titles and scores in the results sheet
  for (var i = 0; i < 9; i++) {
    var legionTitle = "Legion " + (i+1);
    resultsSheet.getRange(i+2, 1).setValue(legionTitle);
    resultsSheet.getRange(i+2, 2).setValue(legionScores[i]);
  }

  // Determine the highest score and set the background color of the corresponding cell(s) to Yellow
  var maxScore = Math.max(...legionScores);
  
  // Clear the background color of all cells in the results sheet excepting the first row
  resultsSheet.getRange(2, 1, resultsSheet.getLastRow()-1, resultsSheet.getLastColumn()).clearFormat();
  
  for (var i = 0; i < 9; i++) {
    if (legionScores[i] === maxScore) {
      resultsSheet.getRange(i+2, 1, 1, 2).setBackground("yellow");
    }
  }
}


function resolute(phNum) {
  var digits = phNum.toString().split("").map(Number);
  var sum = digits.reduce(function(a, b) { return a + b; });

  while (sum > 9) {
    digits = sum.toString().split("").map(Number);
    sum = digits.reduce(function(a, b) { return a + b; });
  }
  
  return sum;
}

function sortSheet() {
  var spreadsheetName = "9LEGIONS"; // Change to the name of your spreadsheet
  var sheetName = "messages"; // Change to the name of your sheet
  var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = spreadsheet.getSheetByName(sheetName);
  var numRows = sheet.getLastRow() - 1;
  
  if (numRows > 0) {
    var range = sheet.getRange(2, 1, numRows, sheet.getLastColumn());
    var values = range.getValues();

    // Sort the range in descending order based on the "DATE" column
    values.sort(function(a, b) {
      var dateA = parseDate(a[0]);
      var dateB = parseDate(b[0]);
      return dateB - dateA;
    });

    // Update the sorted range on the sheet
    range.setValues(values);
  }
}

function parseDate(dateStr) {
  var dateParts = dateStr.split(" ");
  var month = parseMonth(dateParts[0]);
  var day = parseInt(dateParts[1].replace(",", ""));
  var year = parseInt(dateParts[2]);
  var timeParts = dateParts[4].split(":");
  var hour = parseInt(timeParts[0]);
  var minute = parseInt(timeParts[1]);
  if (dateParts[5] == "PM") {
    hour += 12;
  }
  var dateObj = new Date(year, month, day, hour, minute);
  return dateObj;
}

function parseMonth(monthStr) {
  var monthNames = [    "January", "February", "March", "April", "May", "June",    "July", "August", "September", "October", "November", "December"  ];
  return monthNames.indexOf(monthStr);
}


function setTrigger() {
  // Get all existing triggers in the project
  var triggers = ScriptApp.getProjectTriggers();

  // Check if a trigger already exists for the autoexecute function
  var triggerExists = false;
  for (var i = 0; i < triggers.length; i++) {
    if (triggers[i].getHandlerFunction() == 'autoexecute') {
      triggerExists = true;
      break;
    }
  }

  // If a trigger doesn't exist create a new one that runs the autoexecute function every 1 minutes
  if (!triggerExists) {
    ScriptApp.newTrigger('autoexecute')
             .timeBased()
             .everySeconds(60)
             .create();
  }
}

function doGet() {
  var output = HtmlService.createHtmlOutputFromFile('index')
      .setTitle('Sentinel')
      .setSandboxMode(HtmlService.SandboxMode.IFRAME);
  return output;
}






