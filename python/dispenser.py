# dispenser.py

import time
# import csv


# Get current time in nanoseconds since the epoch
current_time_ns = time.time_ns()

print(f"Current time in nanoseconds since the epoch: {current_time_ns}")

# If you want to convert this to a more human-readable format:
from datetime import datetime

current_datetime = datetime.fromtimestamp(current_time_ns / 1e9)
# print(f"Human-readable format: {current_datetime}")

# Note: The human-readable format won't show the nanoseconds, but the precision is still stored in the variable.

# Write to CSV file
# csv_filename = "current_datetime.csv"
# with open(csv_filename, 'w', newline='') as csvfile:
#    csv_writer = csv.writer(csvfile)
#    csv_writer.writerow(['timestamp', 'human_readable'])
#    csv_writer.writerow([current_time_ns, current_datetime])

# print(f"Current time in nanoseconds since the epoch: {current_time_ns}")
# print(f"Human-readable format: {current_datetime}")
# print(f"Data exported to {csv_filename}")
    
