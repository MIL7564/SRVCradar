import 'dart:io';
import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;


// Function to retrieve Legion Number based on CAC
int resolute(String phNum) {
  List<int> digits = phNum.runes.map((rune) => rune - 48).toList();
  int sum = digits.sublist(1, 4).reduce((a, b) => a + b);

  while (sum > 9) {
    digits = sum.toString().runes.map((rune) => rune - 48).toList();
    sum = digits.reduce((a, b) => a + b);
  }

  return sum;
}


Future<String> promptForCAC() async {
  stdout.write('Enter your City Area Code (CAC): ');
  String input = stdin.readLineSync();
  return input.trim();
}

void main() async {
  // Prompt user for CAC input
  String cac = await promptForCAC();

  // Retrieve information from EDGAR database (JSON formatted)
  var companyData = await retrieveCompanyDataFromEDGAR();

  // Create and open SQLite database
  var databasesPath = await getDatabasesPath();
  var path = join(databasesPath, 'company_data.db');
  var database = await openDatabase(path, version: 1,
      onCreate: (Database db, int version) async {
    // Create table in the database to store company information
    await db.execute(
      'CREATE TABLE Companies ('
      'CompanyID INTEGER PRIMARY KEY, '
      'CompanyName TEXT, '
      'AreaCode TEXT, '
      'COGS REAL, '
      'Resolute INTEGER)',
    );
  });

  // Store the retrieved company information in the SQLite database
  for (var company in companyData) {
    // Convert CAC to Resolute
    int resoluteValue = resolute(company['AreaCode']);

    // Insert company data into the database
    await database.transaction((txn) async {
      await txn.rawInsert(
        'INSERT INTO Companies(CompanyName, AreaCode, COGS, Resolute) VALUES(?, ?, ?, ?)',
        [company['CompanyName'], company['AreaCode'], company['COGS'], resoluteValue],
      );
    });
  }

  // Retrieve the total COGS for each Legion
  var results = await database.rawQuery(
    'SELECT Resolute, SUM(COGS) as TotalCOGS '
    'FROM Companies '
    'GROUP BY Resolute '
    'ORDER BY TotalCOGS DESC',
  );

  // Display the rankings for each Legion
  print('Legion Rankings:');
  for (var result in results) {
    print('Legion ${result['Resolute']}: ${result['TotalCOGS']}');
  }

  // Close the database connection
  await database.close();
}

// Function to retrieve company data from EDGAR database
Future<List<Map<String, dynamic>>> retrieveCompanyDataFromEDGAR() async {
  List<Map<String, dynamic>> companyData = [];

  try {
    // Make HTTP request to fetch the list of company CIKs
    var cikResponse = await http.get(Uri.parse('https://data.sec.gov/submissions/cik.json'));
    if (cikResponse.statusCode == 200) {
      List<String> cikList = List<String>.from(json.decode(cikResponse.body));

      // Iterate through each CIK and fetch the company data
      for (var i = 0; i < cikList.length; i++) {
        var cik = cikList[i];

        // Introduce a delay between API requests to adhere to the rate limit
        await Future.delayed(Duration(milliseconds: i * 100));

        // Make HTTP request to fetch the company data for the specific CIK
        var companyResponse = await http.get(Uri.parse('https://data.sec.gov/submissions/$cik.json'));
        if (companyResponse.statusCode == 200) {
          var companyJson = json.decode(companyResponse.body);

          // Extract the required information from the JSON response
          var companyName = companyJson['name'];
          var areaCode = companyJson['phoneAreaCode'];
          var cogs = await fetchCOGS(cik); // Fetch Cost of Goods Sold for the specific CIK

          // Create a map with the extracted data
          var companyMap = {
            'CompanyName': companyName,
            'AreaCode': areaCode,
            'COGS': cogs,
          };

          // Add the company map to the list of company data
          companyData.add(companyMap);
        }
      }
    }
  } catch (e) {
    print('Error retrieving company data from EDGAR database: $e');
  }

  return companyData;
}

// Function to fetch the Cost of Goods Sold (COGS) for a specific CIK
Future<double> fetchCOGS(String cik) async {
  // Introduce a delay between API requests to adhere to the rate limit
  await Future.delayed(const Duration(milliseconds: 500));

  try {
    var url = Uri.parse('https://data.sec.gov/api/xbrl/companyfacts/$cik.json');
    var response = await http.get(url);
    if (response.statusCode == 200) {
      var data = json.decode(response.body);
      // Extract the COGS value from the JSON response based on the desired structure
      var cogs = data['COGS'];
      return cogs?.toDouble() ?? 0.0; // If COGS is not available, return a default value
    } else {
      print('Error fetching COGS for CIK $cik: ${response.statusCode}');
    }
  } catch (e) {
    print('Error fetching COGS for CIK $cik: $e');
  }

  return 0.0; // Return a default value in case of an error or missing data
}
