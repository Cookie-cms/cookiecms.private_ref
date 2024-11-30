# Dockerfile for WebSocket service (example)
FROM php:8.2-cli

# Install required PHP extensions and WebSocket dependencies
RUN apt-get update && apt-get install -y \
    libwebsockets-dev \
    && rm -rf /var/lib/apt/lists/*

# Set the working directory in the container
WORKDIR /application

# Copy the application files into the container
COPY . /application

# Install Composer and Ratchet
# Expose the WebSocket port (optional, if you want it accessible externally)
EXPOSE 8080

# Start the WebSocket server
CMD ["php", "src/ws-server.php"]
