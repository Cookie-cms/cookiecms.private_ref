#!/bin/bash

# Container name (replace with your container name)
# CONTAINER_NAME="cookiecms-php_fpm"

# Find the container ID using the container name and execute a command inside it
docker exec -it php-fpm composer install

# Variables
SQL_SERVER="mysql"
SQL_PORT="3306"  # Default SQL Server port
SQL_USER="cookiecms"
SQL_PASSWORD="cookiecms"
SQL_DATABASE="cookiecms"
SQL_SCRIPT_PATH="./cookiecms.sql"

# Execute the SQL script using sqlcmd
sqlcmd -S $SQL_SERVER,$SQL_PORT -U $SQL_USER -P $SQL_PASSWORD -d $SQL_DATABASE -i $SQL_SCRIPT_PATH

# Check if the command was successful
if [ $? -eq 0 ]; then
    echo "SQL script executed successfully!"
else
    echo "Error executing SQL script."
fi
