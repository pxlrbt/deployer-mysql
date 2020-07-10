# deployer-mysql

An unofficial [Deployer](https://github.com/deployphp/deployer) recipe containing a set of useful tasks for interacting with MySQL.

## Installation

Install via Composer as a dev dependency to your project.

```shell
$ composer require --dev pxlrbt/deployer-mysql
```

## Configuration

For configuring MySQL connection, add the following to your deployer config:

```php
require __DIR__ . '/vendor/pxlrbt/deployer-mysql/recipe/mysql.php';

set('mysql.connection', [
  'host' => 'localhost',
  'port' => 3306,
  'database' => 'your_database_name',
  'username' => 'root',
  'password' => 'root',
]);
```

You can modify the dump file and `mysqldump` options via:
```php
set('mysql.dump', [
  'file' => 'dump.sql',
  'options' => [
      '--skip-comments'
  ]
]);
```

Each option you want to add must be a new entry in the array.

_**Note:** the_ `--skip-comments` _option is the only default option set. So, if you don't have any other options for your setup, you can omit this configuration key entirely._

## Autoloading database credentials

Instead of providing your database credentials inside the deployer config, it's better to load them
from an existing config (e.g. .env file).

### Laravel

There is a recipe that autoloads the credentials from Laravels `.env` file. Just add the recipe to
your deployer file and your ready to go-

```php
require __DIR__ . '/vendor/pxlrbt/deployer-mysql/recipe/laravel.php';
```
If you want to add options (flags) to your `mysqldump` command task, you can do so by adding the "`options`" key to the configuration array, like so;
