<?php
require __DIR__ . '/../bootstrap.php';

use Drrcknlsn\CalendarEventApi\Event\{
    EventFactory,
    EventRepository
};

define('NUM_EVENTS', 50);

$dsn = sprintf(
    'mysql:host=%s;dbname=%s',
    $_ENV['DB_HOST'],
    $_ENV['DB_NAME']
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

$eventFactory = new EventFactory();
$eventRepo = new EventRepository($conn, $eventFactory);
$events = [];

for ($i = 0; $i < NUM_EVENTS; $i++) {
    $event = $eventFactory->createFromArray([
        'title' => substr(uniqid('Title '), 0, 50),
        'date' => date('Y-m-d H:i:s', mt_rand(
            strtotime('2000-01-01'),
            time()
        )),
        'impact' => mt_rand(0, 5),
        'instrument' => substr(uniqid('Instrument '), 0, 50),
        'actual' => mt_rand(0, 9999) / 1000,
        'forecast' => mt_rand(0, 9999) / 1000
    ]);

    $eventRepo->create($event);
}

printf("Created %d mock events.\n", NUM_EVENTS);
