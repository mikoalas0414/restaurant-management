<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register Core Helpers
|--------------------------------------------------------------------------
|
| This line ensures that the core global helpers are
| always given priority one status and that dependencies are installed.
|
*/

$helperPath = __DIR__.'/../vendor/tastyigniter/flame/src/Support/helpers.php';

if (!file_exists($helperPath)) {
    echo 'Missing vendor files, try running "composer install" or use the installer.'.PHP_EOL;
    exit(1);
}

require $helperPath;

/*
|--------------------------------------------------------------------------
| Register The Composer Auto TemplateLoader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';
