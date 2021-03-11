<?php

declare(strict_types=1);

ini_set('display_errors', 'stderr'); // error_log will be reflected properly in roadrunner logs

if (!file_exists("vendor/autoload.php")) {
    exec('composer install');
}

use Spiral\Goridge\StreamRelay;
use Spiral\RoadRunner\Worker;
use Symfony\Component\Dotenv\Dotenv;

require "vendor/autoload.php";

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$server = $kernel->getContainer()->get('Spiral\GRPC\Server');
$relay = new StreamRelay(STDIN, STDOUT);
$worker = new Worker($relay);
$server->serve($worker);
