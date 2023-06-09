/* import 'package:vm/vm.dart'; */
import 'package:flutter/material.dart';
import 'package:path/path.dart';
/* import 'package:path_provider/path_provider.dart'; */
import 'package:sqflite/sqflite.dart';
import 'package:sqflite_common_ffi/sqflite_ffi.dart' as sqflite_ffi;

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

class SentinelApp extends StatefulWidget {
  const SentinelApp({Key? key}) : super(key: key);

  @override
  SentinelAppState createState() => SentinelAppState();
}

class SentinelAppState extends State<SentinelApp> {
  late String cac;
  late bool act;
  late Database db;
  List<Legion>? results; // Make the list nullable
  late Legion winningLegion;

  @override
  void initState() {
    super.initState();
    winningLegion = Legion('', 0); // Initialize winningLegion with a default value
    openDatabaseConnection().then((value) {
      db = value;
      getLegionScores().then((legionScores) {
        setState(() {
          results = legionScores;
          winningLegion = determineWinningLegion();
        });
      });
    });
  }

  Future<Database> openDatabaseConnection() async {
    sqflite_ffi.sqfliteFfiInit(); // Initialize FFI
    final databasesPath = await getDatabasesPath();
    final path = join(databasesPath, 'legion_scores.db');
    return await sqflite_ffi.databaseFactoryFfi.openDatabase(path,
        options: OpenDatabaseOptions(version: 1, onCreate: (db, version) async {
      await db.execute('CREATE TABLE IF NOT EXISTS scores (legion_name TEXT, score INTEGER)');
    }));
  }

  Future<List<Legion>> getLegionScores() async {
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

    List<Legion> scores = [];
    for (String legionName in legionNames) {
      int score = await getLegionScore(legionName);
      scores.add(Legion(legionName, score));
    }
    return scores;
  }

  Future<int> getLegionScore(String legionName) async {
    List<Map<String, dynamic>> result = await db.rawQuery(
        'SELECT score FROM scores WHERE legion_name = ?', [legionName]);
    if (result.isNotEmpty) {
      return result.first['score'] as int;
    }
    return 0;
  }

  Legion determineWinningLegion() {
    var winningLegion = results!.first;
    for (var legion in results!) {
      if (legion.score > winningLegion.score) {
        winningLegion = legion;
      }
    }
    return winningLegion;
  }

  Future<void> updateLegionScore(String legionName, int score) async {
    await db.transaction((txn) async {
      await txn.rawInsert(
          'INSERT OR REPLACE INTO scores(legion_name, score) VALUES(?, ?)',
          [legionName, score]);
    });
  }

  Future<void> handleCACInput(String input) async {
    cac = input.trim();
  }

  Future<void> handleAOKInput(String input) async {
    act = input.trim().toLowerCase() == 'yes';
  }

  Future<void> submitForm() async {
    int userLegion = resolute(cac);
    int currentScore = await getLegionScore(results![userLegion - 1].name);
    int newScore = act ? currentScore + 1 : currentScore;
    await updateLegionScore(results![userLegion - 1].name, newScore);

    List<Legion> updatedScores = await getLegionScores();
    setState(() {
      results = updatedScores;
      winningLegion = determineWinningLegion();
    });
  }

  @override
  Widget build(BuildContext context) {
    if (results == null || winningLegion.name.isEmpty) {
      return Scaffold(
        appBar: AppBar(title: const Text('Sentinel App')),
        body: const Center(child: CircularProgressIndicator()),
      );
    }

    return Scaffold(
      appBar: AppBar(title: const Text('Sentinel App')),
      body: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Text('Enter your City Area Code (CAC):'),
          TextField(onChanged: handleCACInput),
          const SizedBox(height: 16),
          const Text('Report an Act Of Kindness by you to Homeless via typing "yes":'),
          TextField(onChanged: handleAOKInput),
          const SizedBox(height: 16),
          ElevatedButton(onPressed: submitForm, child: const Text('Submit')),
          const SizedBox(height: 32),
          if (winningLegion.name == results![resolute(cac) - 1].name)
            Column(
              children: [
                Text('Congratulations! Your Legion (${winningLegion.name}) is the winner this week.'),
                const Text('You can take the next week off from donating to those homeless.'),
              ],
            )
          else
            const Text('Legions that did not win! Keep up the good work in supporting the homeless.'),
        ],
      ),
    );
  }
}

void main() {
  sqflite_ffi.sqfliteFfiInit(); // Initialize FFI
  runApp(const MaterialApp(
    title: 'Sentinel App',
    home: SentinelApp(),
  ));
}
