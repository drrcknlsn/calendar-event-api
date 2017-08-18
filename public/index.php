<?php
require __DIR__ . '/../app/bootstrap.php';

$app = new \Slim\App();

require __DIR__ . '/../app/dependencies.php';
require __DIR__ . '/../app/routes.php';

$app->run();
