### Requirements

- ul - database POSTGRES;
- ul - PHP 7.4.3
***
Put in command line:
```
$ composer update
$ sudo apt install make
```

### Connect to Postgres database
set your data in to config/database.php
```php
<?php

$db_connection = 'pgsql';
$host = 'localhost';
$db = 'test';             // database name
$user = 'novapc74';       // users name
$password = 'test';       // users pasword
$port = 5432;
```
### Create tables 'users' and 'tasks'
```
$ make migrate
```
### Create user-admin in to users table
```
$ make seed
```
### Refresh all tables
```
$ make refresh
```
### For start application
```
$ make run
```
