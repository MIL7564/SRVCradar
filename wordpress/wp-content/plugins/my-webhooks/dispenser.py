import time

# Get current time in nanoseconds since the epoch
current_time_ns = time.time_ns()

print(f"Current time in nanoseconds since the epoch: {current_time_ns}")

# If you want to convert this to a more human-readable format:
from datetime import datetime

current_datetime = datetime.fromtimestamp(current_time_ns / 1e9)
print(f"Human-readable format: {current_datetime}")

# Note: The human-readable format won't show the nanoseconds, but the precision is still stored in the variable.
