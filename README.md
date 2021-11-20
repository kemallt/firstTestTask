### Requirements

- ul - database POSTGRES;
- ul - PHP 7.4.3
***
Put in command line:
```
$ composer update
$ sudo apt install make
```

### Connect to Mysql database
set your data in to config/database.php
```php
<?php

$db_connection = 'mysql';
$host = 'localhost';
$db = 'test';             // database name
$user = 'root';       // users name
$password = 'root';       // users pasword
$port = 3306;
```
### Create tables 'users' and 'tasks'
```
$ make migrate
```
### Create user-admin in to users table and seed 10 tasks to task table
```
$ make seed
```
### Refresh all tables and reseed them
```
$ make refresh
```
### For start application
```
$ make run
```
