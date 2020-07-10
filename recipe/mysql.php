<?php

/**
 * An unofficial Deployer recipe containing a set of useful
 * tasks for interacting with MySQL locally & remotely.
 *
 * Based on https://github.com/jordanbrauer/deployer-mysql
 */

namespace Deployer;

// Config
set('bin/mysqldump', function () {
    return locateBinaryPath('mysqldump');
});

set('bin/mysql', function () {
    return locateBinaryPath('mysql');
});

set('mysql.before_cmd', 'true');

set('mysql.connection', [
    'host' => 'localhost',
    'port' => '3306',
    'database' => null,
    'username' => 'root',
    'password' => '',
]);

set('mysql.dump', [
    'file' => 'dump.sql',
    'options' => [
        '--skip-comments',
    ]
]);

// Tasks
desc('Dump the database to an SQL file');
task('mysql:dump', function () {
    $conn = get('mysql.connection');
    $dump = get('mysql.dump');
    $dump['options'] = implode(" ", $dump['options']);

    if (!$dump['file']) {
        throw new Exception('"mysql.dump.file" has not been set.');
    }

    run("{{mysql.before_cmd}} && {{bin/mysqldump}} -h {$conn['host']} -P {$conn['port']} -u {$conn['username']} -p{$conn['password']} {$dump['options']} {$conn['database']} > {{release_path}}/{$dump['file']}");
});

desc('Restore the database from an SQL file');
task('mysql:restore', function () {
    $conn = get('mysql.connection');
    $dump = get('mysql.dump');

    if (!$conn['database']) {
        throw new Exception('"mysql.connection.database" has not been set.');
    }

    if (!$dump['file']) {
        throw new Exception('"mysql.dump.file" has not been set.');
    }

    run("{{mysql.before_cmd}} && {{bin/mysql}} -h {$conn['host']} -P {$conn['port']} -u {$conn['username']} -p{$conn['password']} {$conn['database']} < {$dump['file']}");
});

desc('Download the current remote SQL dump to local');
task('mysql:download', function () {
    $dump = get('mysql.dump');

    if (!$dump['file']) {
        throw new Exception('"mysql.dump.file" option has not been set.');
    }

    download("{{release_path}}/{$dump['file']}", "{$dump['file']}");
});

desc('Upload the current local SQL dump to remote');
task('mysql:upload', function () {
    $dump = get('mysql.dump');

    if (!$dump['file']) {
        throw new Exception('"mysql.dump.file" option has not been set.');
    }

    upload("{$dump['file']}", "{{release_path}}/{$dump['file']}");
});

desc('Removes the SQL dump file.');
task('mysql:cleanup', function () {
    $dump = get('mysql.dump');

    if (!$dump['file']) {
        throw new Exception('"mysql.dump.file" option has not been set.');
    }

    run("rm {{release_path}}/{$dump['file']}");
});

desc('Fetch a fresh copy of the remote SQL dump');
task('mysql:pull', [
    'mysql:dump',
    'mysql:download',
    'mysql:cleanup',
]);
