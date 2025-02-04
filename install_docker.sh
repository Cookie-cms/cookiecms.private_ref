# This file is part of CookieCms.
#
# CookieCms is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# CookieCms is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with CookieCms. If not, see <http://www.gnu.org/licenses/>.

# #!/bin/bash

# # Container name (replace with your container name)
# # CONTAINER_NAME="cookiecms-php_fpm"

# # Find the container ID using the container name and execute a command inside it
# docker exec -it php-fpm composer install

# # Variables
# SQL_SERVER="mysql"
# SQL_PORT="3306"  # Default SQL Server port
# SQL_USER="cookiecms"
# SQL_PASSWORD="cookiecms"
# SQL_DATABASE="cookiecms"
# SQL_SCRIPT_PATH="./cookiecms.sql"

# # Execute the SQL script using sqlcmd
# sqlcmd -S $SQL_SERVER,$SQL_PORT -U $SQL_USER -P $SQL_PASSWORD -d $SQL_DATABASE -i $SQL_SCRIPT_PATH

# # Check if the command was successful
# if [ $? -eq 0 ]; then
#     echo "SQL script executed successfully!"
# else
#     echo "Error executing SQL script."
# fi

#!/bin/bash

# Настройки базы данных
DB_HOST="localhost"
DB_USER="root"
DB_PASS="admin"
DB_NAME="cookiecms"
SQL_FILE="./cookiecms.sql" # Укажите путь к вашему .sql файлу

# Проверка наличия SQL файла
if [ ! -f "$SQL_FILE" ]; then
    echo "Ошибка: Файл $SQL_FILE не найден."
    exit 1
fi

# Удаление существующей базы данных
echo "Удаление базы данных $DB_NAME..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "DROP DATABASE IF EXISTS \`$DB_NAME\`;"

# Создание новой базы данных
echo "Создание базы данных $DB_NAME..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE \`$DB_NAME\`;"

# Импорт структуры
echo "Импорт структуры базы данных из $SQL_FILE..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$SQL_FILE"

# Проверка результата
if [ $? -eq 0 ]; then
    echo "База данных $DB_NAME успешно пересоздана и структура импортирована."
else
    echo "Ошибка при создании базы данных или импорте структуры."
    exit 1
fi
