from sentinel import resolute
from flask import Flask, render_template, request
import sqlite3

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/submit', methods=['POST'])
def submit():
    cac = request.form['cac']
    aok = request.form['aok']

    db = sqlite3.connect('legion_scores.db')
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

if __name__ == '__main__':
    app.run()
