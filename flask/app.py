from scripts.sentinel import resolute
from flask import Flask, render_template, request, jsonify, send_from_directory
import sqlite3
import os

app = Flask(__name__, static_folder='static')

# Get the absolute path to the database file
database_path = os.path.join(app.root_path, 'data', 'legion_scores.db')

@app.route('/')
def index():
    return render_template('index.html')

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
    cursor.execute("INSERT OR REPLACE INTO scores (legion_name, score) VALUES (?, ?)",
                   (f"Legion {user_legion}", new_score))
    db.commit()

    db.close()

    return '', 204

@app.route('/legion-scores')
def get_legion_scores():
    db = sqlite3.connect(database_path)
    cursor = db.cursor()

    # Fetch all the Legion scores from the database
    cursor.execute("SELECT * FROM scores")
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

@app.route('/static/<path:filename>')
def serve_static(filename):
    return send_from_directory(app.static_folder, filename)

if __name__ == '__main__':
    app.run()
