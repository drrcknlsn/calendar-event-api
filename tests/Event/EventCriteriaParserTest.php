<?php
declare(strict_types=1);

namespace Test\Drrcknlsn\CalendarEventApi\Event;

use DateTimeImmutable;
use Drrcknlsn\CalendarEventApi\Event\EventCriteriaParser;
use PHPUnit\Framework\TestCase;

class EventCriteriaParserTest extends TestCase
{
    public function setUp()
    {
        $this->criParser = new EventCriteriaParser();
    }

    public function testImpactJustValue()
    {
        $expr = $this->criParser->parse(['impact' => '1']);

        $this->assertArraySubset(
            [['impact', '=', 1]],
            $expr
        );
    }

    public function testImpactGtValue()
    {
        $expr = $this->criParser->parse(['impact' => '>1']);

        $this->assertArraySubset(
            [['impact', '>', 1]],
            $expr
        );
    }

    public function testImpactLtValue()
    {
        $expr = $this->criParser->parse(['impact' => '<1']);

        $this->assertArraySubset(
            [['impact', '<', 1]],
            $expr
        );
    }

    public function testImpactGteValue()
    {
        $expr = $this->criParser->parse(['impact' => '>=1']);

        $this->assertArraySubset(
            [['impact', '>=', 1]],
            $expr
        );
    }

    public function testImpactLteValue()
    {
        $expr = $this->criParser->parse(['impact' => '<=1']);

        $this->assertArraySubset(
            [['impact', '<=', 1]],
            $expr
        );
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testImpactInvalidOpWithValidValue()
    {
        $this->criParser->parse(['impact' => '!1']);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testImpactValidOpWithInvalidValue()
    {
        $this->criParser->parse(['impact' => '>=foo']);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testImpactInvalidValue()
    {
        $this->criParser->parse(['impact' => '123abc']);
    }

    public function testFromDate()
    {
        $expr = $this->criParser->parse(['from_date' => '2000-01-01']);

        $this->assertArraySubset(
            [['date', '>=', new DateTimeImmutable('2000-01-01')]],
            $expr
        );
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testFromDateInvalidValue()
    {
        $this->criParser->parse(['from_date' => 'foo']);
    }

    public function testToDate()
    {
        $expr = $this->criParser->parse(['to_date' => '2000-01-01']);

        $this->assertArraySubset(
            [['date', '<=', new DateTimeImmutable('2000-01-01')]],
            $expr
        );
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testToDateInvalidValue()
    {
        $this->criParser->parse(['to_date' => 'foo']);
    }
}
