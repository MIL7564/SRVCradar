from scripts.sentinel import resolute
from flask import Flask, render_template, request, jsonify, send_from_directory
import sqlite3
import os

app = Flask(__name__, static_folder='')

# Get the absolute path to the database file
database_path = os.path.join(app.root_path, 'data', 'legion_scores.db')

# Initialize the database with nine legions if it's empty
def initialize_database():
    db = sqlite3.connect(database_path)
    cursor = db.cursor()
    cursor.execute("SELECT count(*) FROM scores")
    result = cursor.fetchone()
    count = result[0] if result else 0
    if count == 0:
        for legion_num in range(1, 10):
            cursor.execute("INSERT INTO scores (legion_name, score) VALUES (?, ?)", (f"Legion {legion_num}", 0))
        db.commit()
    db.close()

@app.route('/')
def index():
    return send_from_directory('.', 'index.html')

@app.route('/submit', methods=['POST'])
def submit():
    cac = request.form['cac']
    aok = request.form['aok']

    db = sqlite3.connect(database_path)
    cursor = db.cursor()

    # Update the score in the database
    user_legion = resolute(cac)
    cursor.execute("SELECT score FROM scores WHERE legion_name = ?", (f"Legion {user_legion}",))
    result = cursor.fetchone()
    score = result[0] if result else 0
    new_score = score + (1 if aok.lower() == 'yes' else 0)
    cursor.execute("UPDATE scores SET score = ? WHERE legion_name = ?", (new_score, f"Legion {user_legion}"))
    db.commit()

    db.close()

    return '', 204

@app.route('/legion-scores')
def get_legion_scores():
    db = sqlite3.connect(database_path)
    cursor = db.cursor()

    # Fetch the scores for the nine legions
    cursor.execute("SELECT * FROM scores WHERE legion_name LIKE 'Legion %' ORDER BY legion_name")
    results = cursor.fetchall()

    # Convert the results into a list of dictionaries
    scores = []
    for row in results:
        legion_name, score, date = row
        scores.append({
            'legion_name': legion_name,
            'score': score,
            'date': date
        })

    db.close()

    return jsonify(scores)

if __name__ == '__main__':
    initialize_database()
    app.run()
