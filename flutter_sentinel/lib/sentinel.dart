/* sentinel.dart */
import 'dart:io';
import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';

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

class Legion {
  String name;
  int score;

  Legion(this.name, this.score);
}

Future<String> promptForCAC() async {
  stdout.write('Enter your City Area Code (CAC): ');
  String input = stdin.readLineSync()!;
  return input.trim();
}

Future<bool> promptForAOK() async {
  stdout.write('Report an Act Of Kindness by you to Homeless via typing "yes": ');
  String input = stdin.readLineSync()!;
  return input.trim().toLowerCase() == 'yes';
}

Future<Database> openDatabaseConnection() async {
  String databasesPath = await getDatabasesPath();
  String path = join(databasesPath, 'legion_scores.db');
  return await openDatabase(path, version: 1, onCreate: (Database db, int version) async {
    await db.execute('CREATE TABLE IF NOT EXISTS scores (legion_name TEXT, score INTEGER)');
  });
}

Future<void> updateLegionScore(Database db, String legionName, int score) async {
  await db.transaction((txn) async {
    await txn.rawInsert('INSERT OR REPLACE INTO scores(legion_name, score) VALUES(?, ?)', [legionName, score]);
  });
}

Future<int> getLegionScore(Database db, String legionName) async {
  List<Map<String, dynamic>> result = await db.rawQuery('SELECT score FROM scores WHERE legion_name = ?', [legionName]);
  if (result.isNotEmpty) {
    return result.first['score'] as int;
  }
  return 0;
}

void main() async {
  // Prompt user for CAC input
  String cac = await promptForCAC();

  // Prompt user for Act Of Kindness
  bool act = await promptForAOK();

  // Open the database connection
  Database db = await openDatabaseConnection();

  // Define the Legion names
  List<String> legionNames = [
    'Legion 1',
    'Legion 2',
    'Legion 3',
    'Legion 4',
    'Legion 5',
    'Legion 6',
    'Legion 7',
    'Legion 8',
    'Legion 9',
  ];

  // Update Legion score based on user input
  int userLegion = resolute(cac);
  int currentScore = await getLegionScore(db, legionNames[userLegion - 1]);
  int newScore = act ? currentScore + 1 : currentScore;
  await updateLegionScore(db, legionNames[userLegion - 1], newScore);

  // Retrieve Legion scores from the database
  List<Legion> results = [];
  for (String legionName in legionNames) {
    int score = await getLegionScore(db, legionName);
    results.add(Legion(legionName, score));
  }

  // Determine the winning Legion
  var winningLegion = results.first;

  for (var legion in results) {
    if (legion.score > winningLegion.score) {
      winningLegion = legion;
    }
  }

  print('Winning Legion: ${winningLegion.name}');

  // Display the results as a table
  print('Legion\t\tScore');
  print('-------------------');
  for (var legion in results) {
    print('${legion.name}\t\t${legion.score}');
  }

  // Highlight winning Legion and advise next week off from donating to the homeless
  if (winningLegion.name == legionNames[userLegion - 1]) {
    print('Congratulations! Your Legion (${winningLegion.name}) is the winner this week.');
    print('You can take the next week off from donating to those homeless.');
  } else {
    print('Legions that did not win! Keep up the good work in supporting the homeless.');
  }

  // Close the database connection
  await db.close();
}
