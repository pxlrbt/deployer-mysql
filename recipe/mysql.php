<?php

/**
 * An unofficial Deployer recipe containing a set of useful
 * tasks for interacting with MySQL locally & remotely.
 *
 * Based on https://github.com/jordanbrauer/deployer-mysql
 */

namespace Deployer;

// Set defaults
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
    'schema' => null,
    'username' => 'root',
    'password' => '',
]);

set('mysql.dump', [
    'file' => null,
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
        throw new Exception('The mysql.dump.file has not been set. Please set this in your deployer configuration.');
    }

    run("{{mysql.before_cmd}} && {{bin/mysqldump}} -h {$conn['host']} -P {$conn['port']} -u {$conn['username']} -p{$conn['password']} {$dump['options']} {$conn['schema']} > {{release_path}}/{$dump['file']}");
});

desc('Restore the database from an SQL file');
task('mysql:restore', function () {
    $conn = get('mysql.connection');
    $dump = get('mysql.dump');

    if (!$conn['schema']) {
        throw new Exception('A schema has not been specific for use. Please set the mysql.connection.schema in your deployer configuration');
    }

    if (!$dump['file']) {
        throw new Exception('The mysql.dump.file has not been set. Please set this in your deployer configuration.');
    }

    run("{{mysql.before_cmd}} && {{bin/mysql}} -h {$conn['host']} -P {$conn['port']} -u {$conn['username']} -p{$conn['password']} {$conn['schema']} < {$dump['file']}");
});

desc('Download the current remote SQL dump to local');
task('mysql:download', function () {
    $dump = get('mysql.dump');

    if (!$dump['file']) {
        throw new Exception('The mysql.dump.file has not been set. Please set this in your deployer configuration.');
    }

    download("{{release_path}}/{$dump['file']}", "{$dump['file']}");
});

task('mysql:cleanup', function () {
    $dump = get('mysql.dump');

    if (!$dump['file']) {
        throw new Exception('The mysql.dump.file has not been set. Please set this in your deployer configuration.');
    }

    run("rm {{release_path}}/{$dump['file']}");
});

desc('Upload the current local SQL dump to remote');
task('mysql:upload', function () {
    $dump = get('mysql.dump');

    if (!$dump['file']) {
        throw new Exception('The mysql.dump.file has not been set. Please set this in your deployer configuration.');
    }

    upload("{$dump['file']}", "{{release_path}}/{$dump['file']}");
});

desc('Fetch a fresh copy of the remote SQL dump');
task('mysql:pull', [
    'mysql:dump',
    'mysql:download',
    'mysql:cleanup',
]);
