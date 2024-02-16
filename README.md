## REST API in raw PHP

This example shows demonstrate a simple REST API in core PHP.

**Prerequisites:** PHP, Composer and MySQL.

### Getting Started
Clone this repository using the following commands:

```sh
git clone git@github.com:smronju/raw-php-api.git
cd raw-php-api
```

### Configure the application

Create the database and user for the project:

```sh
mysql -uroot -p
CREATE DATABASE php-api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'api_user'@'localhost' identified by 'api_password';
GRANT ALL on php-api.* to 'api_user'@'localhost';
quit
```

Copy and edit the `.env` file and enter your database details:

```sh
cp .env.example .env
```

Install the project dependencies and start the PHP server:

```sh
composer install
php -S 127.0.0.1:8000
```
Now create table and seed some data by [127.0.0.1:8000/subscriber/seed](127.0.0.1:8000/subscriber/seed).

Navigating [127.0.0.1:8000/subscriber](127.0.0.1:8000/subscriber) should return a subscriber paginated list.
