#!/bin/bash

# Function to display a dynamically sized progress bar
display_dynamic_progress_bar() {
    local total_steps=$1
    local progress=$2
    local terminal_width=$(tput cols)

    # Deduct 10 to leave space for percentage display and brackets
    let bar_width=terminal_width-10

    let filled_slots=progress*bar_width/total_steps
    bar=""

    # Generate the filled portion of the bar
    for ((i=0; i<$filled_slots; i++)); do
        bar="${bar}#"
    done

    # Generate the unfilled portion of the bar
    for ((i=filled_slots; i<bar_width; i++)); do
        bar="${bar}-"
    done

    # Calculate the percentage of completion
    let percentage=progress*100/total_steps

    # If the progress reaches 100%, display the bar in green
    if ((progress == 100)); then
        echo -ne "\033[32m"  # Set color to green
    elif ((progress == 90)); then
        echo -ne "\033[31m"  # Set color to red for crash (if progress is 90%)
    else
        echo -ne "\033[0m"   # Reset color for normal
    fi

    # Display the bar and percentage on the same line
    echo -ne "\r[${bar}] ${percentage}% "

    # Reset color after the bar
    echo -ne "\033[0m"
}

# Function to increase the progress by a given amount
barup() {
    local increment=$1
    progress=$((progress + increment))

    # Ensure progress does not exceed 100%
    if ((progress > 100)); then
        progress=100
    fi

    # Display the updated progress bar
    display_dynamic_progress_bar 100 $progress
}

# Function to simulate a crash, changing color to red and showing message at current progress
crashbar() {
    # Display the progress bar in red at the current progress
    echo -ne "\033[31m"  # Set color to red for crash
    display_dynamic_progress_bar 100 $progress
    echo -ne "\033[0m"    # Reset color

    # Display crash message
    echo -e "\n[CRASH] Process terminated at ${progress}%."

    # Exit the script
    exit 0
}

# Initial progress
progress=0

# Main execution
echo "Starting the operation..."
# Display initial progress
display_dynamic_progress_bar 100 $progress

# Usage examples:
barup 99   # Increase progress by 40
sleep 2
barup 1   # Increase progress by 40
# sleep 3
# barup 10   # Increase progress by 10
# sleep 2
# crashbar    # Simulate crash at current progress
