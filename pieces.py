import os
import tkinter as tk
from tkinter import simpledialog, messagebox

def count_words_in_file(file_path):
    """
    Counts the number of words in a file.
    """
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
        words = content.split()
        return len(words)

def split_file_by_word_count(file_path, chunk_size=1000):
    """
    Splits a file into multiple .txt files based on the word count.
    Each split file will have at most 'chunk_size' number of words.
    """
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
        words = content.split()
        num_words = len(words)

        # Determine the number of chunks required
        num_chunks = (num_words + chunk_size - 1) // chunk_size

        # Create and save the split files in the "pieces" folder
        output_folder = os.path.join(os.getcwd(), "pieces")
        os.makedirs(output_folder, exist_ok=True)

        file_name = os.path.basename(file_path)
        for i in range(num_chunks):
            start_idx = i * chunk_size
            end_idx = (i + 1) * chunk_size
            chunk_content = ' '.join(words[start_idx:end_idx])
            new_file_path = os.path.join(output_folder, f"{file_name[:-4]}_{i+1}.txt")  # Assuming the original file is in .txt format
            with open(new_file_path, 'w', encoding='utf-8') as chunk_file:
                chunk_file.write(chunk_content)

def process_folder_and_split():
    """
    Process a folder by asking for both the folder name and the filename to split,
    and then performing the file search and splitting.
    """
    folder_name = simpledialog.askstring("Input", "Enter the folder name where the file you want to split lies:")
    if not folder_name:
        messagebox.showwarning("Warning", "Folder name cannot be empty.")
        return

    filename_to_split = simpledialog.askstring("Input", "Enter the filename to split within the folder:")
    if not filename_to_split:
        messagebox.showwarning("Warning", "Filename cannot be empty.")
        return

    for root, dirs, files in os.walk(os.getcwd()):
        if folder_name in dirs:
            folder_path = os.path.join(root, folder_name)
            file_path = os.path.join(folder_path, filename_to_split)

            if not os.path.exists(folder_path) or not os.path.isfile(file_path):
                messagebox.showerror("Error", "Invalid folder or file path.")
                return

            word_count = count_words_in_file(file_path)
            if word_count > 1000:
                split_file_by_word_count(file_path)
                messagebox.showinfo("Success", f"File '{filename_to_split}' has been split.")
            else:
                messagebox.showinfo("Information", f"File '{filename_to_split}' does not require splitting.")
            return

    messagebox.showwarning("Warning", "Folder not found in the file structure.")

if __name__ == "__main__":
    root = tk.Tk()
    root.withdraw()  # Hide the main tkinter window

    process_folder_and_split()
