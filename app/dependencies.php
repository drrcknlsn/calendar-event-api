<?php
use Drrcknlsn\CalendarEventApi\Event\{
    EventCriteriaParser,
    EventFactory,
    EventRepository
};

$container = $app->getContainer();

$container['db'] = function () {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s',
        $_ENV['DB_HOST'],
        $_ENV['DB_NAME']
    );

    return new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
};

$container['eventFactory'] = function ($c) {
    return new EventFactory();
};

$container['eventRepo'] = function ($c) {
    return new EventRepository(
        $c['db'],
        $c['eventFactory']
    );
};

$container['criteriaParser'] = function () {
    return new EventCriteriaParser();
};
