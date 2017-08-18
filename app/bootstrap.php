<?php
ini_set('xdebug.default_enable', false);
ini_set('html_errors', false);
require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();
