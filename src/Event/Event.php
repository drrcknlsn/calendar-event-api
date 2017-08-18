<?php
declare(strict_types=1);

namespace Drrcknlsn\CalendarEventApi\Event;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;

class Event implements JsonSerializable
{
    private $id;
    private $title;
    private $date;
    private $impact;
    private $instrument;
    private $actual;
    private $forecast;

    public function __construct(
        int $id = null,
        string $title = null,
        DateTimeImmutable $date = null,
        int $impact = null,
        string $instrument = null,
        float $actual = null,
        float $forecast = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->date = $date;
        $this->impact = $impact;
        $this->instrument = $instrument;
        $this->actual = $actual;
        $this->forecast = $forecast;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate($date): void
    {
        if ($date instanceof DateTimeInterface) {
            $this->date = clone $date;
        } else {
            $this->date = new DateTimeImmutable($date);
        }
    }

    public function getImpact(): ?int
    {
        return $this->impact;
    }

    public function setImpact(int $impact): void
    {
        $this->impact = $impact;
    }

    public function getInstrument(): ?string
    {
        return $this->instrument;
    }

    public function setInstrument(string $instrument): void
    {
        $this->instrument = $instrument;
    }

    public function getActual(): ?float
    {
        return $this->actual;
    }

    public function setActual(float $actual): void
    {
        $this->actual = $actual;
    }

    public function getForecast(): ?float
    {
        return $this->forecast;
    }

    public function setForecast(float $forecast): void
    {
        $this->forecast = $forecast;
    }

    public function toArray()
    {
        $date = $this->getDate();

        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'date' => isset($date)
                ? $date->format(DateTime::ISO8601)
                : null,
            'impact' => $this->getImpact(),
            'instrument' => $this->getInstrument(),
            'actual' => $this->getActual(),
            'forecast' => $this->getForecast()
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
