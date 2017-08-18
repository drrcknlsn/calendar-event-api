<?php
declare(strict_types=1);

namespace Drrcknlsn\CalendarEventApi\Event;

use DateTimeImmutable;
use Exception;
use RuntimeException;
use UnexpectedValueException;

class EventCriteriaParser
{
    const OPERATORS = ['<', '>', '<=', '>='];

    /**
     * Parses the provided array of query parameters, and returns an array
     * of expression tuples for use in corresponding database queries.
     *
     * @param string[] $queryParams The URL query parameters.
     * @return mixed[][] The expressions.
     */
    public function parse(array $queryParams): array
    {
        $expr = [];

        foreach ($queryParams as $key => $value) {
            switch ($key) {
                case 'impact':
                    $regex = sprintf(
                        '/^(%s)?(\d+)$/',
                        implode('|', array_map(function ($op) {
                            return preg_quote($op, '/');
                        }, self::OPERATORS))
                    );

                    if (!preg_match($regex, $value, $matches)) {
                        throw new UnexpectedValueException(sprintf(
                            "Unexpected 'impact' parameter value: %s",
                            $value
                        ));
                    }

                    $expr[] = ['impact', $matches[1] ?: '=', (int)$matches[2]];
                    break;

                case 'from_date':
                    try {
                        $date = new DateTimeImmutable($value);
                    } catch (Exception $e) {
                        throw new UnexpectedValueException(sprintf(
                            "Unexpected 'from_date' parameter value: %s",
                            $value
                        ), 0, $e);
                    }

                    $expr[] = ['date', '>=', $date];
                    break;

                case 'to_date':
                    try {
                        $date = new DateTimeImmutable($value);
                    } catch (Exception $e) {
                        throw new UnexpectedValueException(sprintf(
                            "Unexpected 'to_date' parameter value: %s",
                            $value
                        ), 0, $e);
                    }

                    $expr[] = ['date', '<=', $date];
                    break;

                case 'instrument':
                    $expr[] = ['instrument', '=', $value];
                    break;

                default:
                    // Ignore unrecognized query keys
            }
        }

        return $expr;
    }
}
