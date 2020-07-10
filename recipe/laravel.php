<?php


namespace Deployer;

set('mysql.before_cmd', 'source {{release_path}}/.env');

set('mysql.connection', [
    'host' => '$DB_HOST',
    'port' => '$DB_PORT',
    'database' => '$DB_DATABASE',
    'username' => '$DB_USERNAME',
    'password' => '$DB_PASSWORD',
]);
