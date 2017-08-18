<?php
declare(strict_types=1);

namespace Drrcknlsn\CalendarEventApi\Event;

use DateTimeImmutable;
use UnexpectedValueException;

class EventFactory
{
    /**
     * Creates an event from an associative array of event data.
     *
     * @param mixed[] $data The event data.
     * @return Event The created event.
     */
    public function createFromArray(array $data): Event
    {
        if (isset($data['id'])
            && ($id = filter_var($data['id'], FILTER_VALIDATE_INT)) === false
        ) {
            throw new UnexpectedValueException(sprintf(
                'Invalid integer: %s',
                $data['id']
            ));
        }

        if (isset($data['date'])) {
            try {
                $date = new DateTimeImmutable($data['date']);
            } catch (Exception $e) {
                throw new UnexpectedValueException(sprintf(
                    'Invalid date: %s',
                    $data['date']
                ), 0, $e);
            }
        }

        if (isset($data['impact'])
            && ($impact = filter_var($data['impact'], FILTER_VALIDATE_INT)) === false
        ) {
            throw new UnexpectedValueException(sprintf(
                'Invalid integer: %s',
                $data['impact']
            ));
        }

        if (isset($data['actual'])
            && ($actual = filter_var($data['actual'], FILTER_VALIDATE_FLOAT)) === false
        ) {
            throw new UnexpectedValueException(sprintf(
                'Invalid float: %s',
                $data['actual']
            ));
        }

        if (isset($data['forecast'])
            && ($forecast = filter_var($data['forecast'], FILTER_VALIDATE_FLOAT)) === false
        ) {
            throw new UnexpectedValueException(sprintf(
                'Invalid float: %s',
                $data['forecast']
            ));
        }

        return new Event(
            $id ?? null,
            $data['title'] ?? null,
            $date ?? null,
            $impact ?? null,
            $data['instrument'] ?? null,
            $actual ?? null,
            $forecast ?? null
        );
    }
}
