<?php

declare(strict_types=1);

ini_set('display_errors', 'stderr'); // error_log will be reflected properly in roadrunner logs

/*
if (!file_exists("vendor/autoload.php")) {
    exec('composer install');
}
*/

require dirname(__DIR__).'/config/bootstrap.php';

$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$kernel->getContainer()->get('zolex.grpc.server')->serve();
