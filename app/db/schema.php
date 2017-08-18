<?php
require __DIR__ . '/../bootstrap.php';

$dsn = sprintf(
    'mysql:host=%s',
    $_ENV['DB_HOST']
);

try {
    $conn = new PDO(
        $dsn,
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
} catch (PDOException $e) {
    printf("Connection failed: %s\n", $e->getMessage());
    exit(1);
}

$sql = sprintf(
    'CREATE DATABASE %s',
    $_ENV['DB_NAME']
);

try {
    $conn->exec($sql);
} catch (PDOException $e) {
    printf("Could not create database: %s\n", $e->getMessage());
    exit(1);
}

$sql = sprintf(
    'USE %s',
    $_ENV['DB_NAME']
);

try {
    $conn->exec($sql);
} catch (PDOException $e) {
    printf(
        "Could not switch to %s: %s\n",
        $_ENV['DB_NAME'],
        $e->getMessage()
    );
    exit(1);
}

$sql = <<<SQL
CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  `impact` int(10) unsigned NOT NULL,
  `instrument` varchar(50) NOT NULL,
  `actual` float NOT NULL,
  `forecast` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

try {
    $conn->exec($sql);
} catch (PDOException $e) {
    printf("Could not create events table: %s\n", $e->getMessage());
    exit(1);
}

echo "Created schema.\n";
