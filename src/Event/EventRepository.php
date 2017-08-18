<?php
declare(strict_types=1);

namespace Drrcknlsn\CalendarEventApi\Event;

use DateTime;
use DateTimeInterface;
use PDO;
use RuntimeException;
use UnexpectedValueException;

class EventRepository
{
    private $conn;
    private $factory;

    public function __construct(PDO $conn, EventFactory $factory)
    {
        $this->conn = $conn;
        $this->factory = $factory;
    }

    /**
     * Retrieves events matching the provided criteria.
     *
     * @param mixed[] $cri The criteria.
     * @return Event[]
     */
    public function findByCri(array $cri = []): array
    {
        [$where, $params] = $this->buildWhereClause($cri);

        $sql = 'SELECT * FROM events';

        if ($where) {
            $sql .= sprintf(
                ' WHERE %s',
                $where
            );
        }

        $st = $this->conn->prepare($sql);
        $st->execute($params);
        $events = [];

        foreach ($st as $row) {
            $events[] = $this->factory->createFromArray($row);
        }

        return $events;
    }

    /**
     * Retrieves the event corresponding to the provided ID, or
     * null if one does not exist.
     *
     * @param int $id The event's ID.
     * @return Event|null
     */
    public function findById(int $id): ?Event
    {
        $events = $this->findByCri([
            ['id', '=', $id]
        ]);

        if (empty($events)) {
            return null;
        }

        return $events[0];
    }

    /**
     * Creates the provided event.
     *
     * @param Event $event The event to create.
     * @return int The new event's ID.
     */
    public function create(Event $event): int
    {
        $sql = <<<SQL
INSERT INTO events (
  title, `date`, impact, instrument, actual, forecast
) VALUES (
  :title, :date, :impact, :instrument, :actual, :forecast
)
SQL;

        $st = $this->conn->prepare($sql);
        $st->execute([
            ':actual' => $event->getActual(),
            ':date' => $event->getDate()
                ? $event->getDate()->format(DateTime::ISO8601)
                : null,
            ':impact' => $event->getImpact(),
            ':instrument' => $event->getInstrument(),
            ':forecast' => $event->getForecast(),
            ':title' => $event->getTitle(),
        ]);

        return (int)$this->conn->lastInsertId();
    }

    /**
     * Updates the provided event.
     *
     * @param Event $event The event to update.
     * @param string[] $fields The fields to update.
     * @throws RuntimeException when the event is missing an ID.
     */
    public function update(Event $event, array $fields = []): void
    {
        if ($event->getId() === null) {
            throw new RuntimeException('Event is missing ID');
        }

        if (empty($fields)) {
            $fields = array_filter(array_keys($event->toArray()), function ($field) {
                return $field !== 'id';
            });
        }

        if (in_array('id', $fields)) {
            throw new UnexpectedValueException("Can't update id");
        }

        $toBeUpdated = array_intersect_key(
            $event->toArray(),
            array_fill_keys($fields, null)
        );

        [$set, $setParams] = $this->buildSetClause($toBeUpdated);
        [$where, $whereParams] = $this->buildWhereClause([
            ['id', '=', $event->getId()]
        ]);

        $sql = sprintf(
            'UPDATE events SET %s WHERE %s',
            $set,
            $where
        );

        $st = $this->conn->prepare($sql);
        $st->execute(array_merge($setParams, $whereParams));
    }

    /**
     * Builds the SET SQL clause and corresponding bind parameters.
     *
     * @param mixed[] The data.
     * @return mixed[] The SQL fragment and bind paramters.
     */
    private function buildSetClause(array $data)
    {
        $set = [];
        $params = [];
        $i = 0;

        foreach ($data as $field => $value) {
            $param = ':s' . $i++;
            $params[$param] = $value instanceof DateTimeInterface
                ? $value->format(DateTime::ISO8601)
                : $value;
            $set[] = sprintf(
                '`%s` = %s',
                $field,
                $param
            );
        }

        return [
            implode(', ', $set),
            $params
        ];
    }

    /**
     * Builds the WHERE SQL clause and corresponding bind parameters.
     *
     * @param mixed[][] The expressions, as key/operator/value tuplets.
     * @return mixed[] The SQL fragment and bind paramters.
     */
    private function buildWhereClause(array $expr)
    {
        $where = [];
        $params = [];
        $i = 0;

        foreach ($expr as [$field, $op, $value]) {
            $param = ':w' . $i++;
            $params[$param] = $value instanceof DateTimeInterface
                ? $value->format(DateTime::ISO8601)
                : $value;
            $where[] = sprintf(
                '%s %s %s',
                $field,
                $op,
                $param
            );
        }

        return [
            implode(' AND ', $where),
            $params
        ];
    }
}
