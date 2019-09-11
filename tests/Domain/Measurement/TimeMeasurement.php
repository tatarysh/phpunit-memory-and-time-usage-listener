<?php

namespace PhpunitMemoryAndTimeUsageListener\Domain\Measurement;

/**
 * Class TimeMeasurement
 */
class TimeMeasurement implements Measurement
{
    /**
     * @var float
     */
    private $quantity;

    /**
     * TimeMeasurement constructor.
     *
     * @param  float  $quantity
     */
    public function __construct(float $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function unit(): string
    {
        return 'microsecond';
    }

    /**
     * @return float
     */
    public function score(): float
    {
        return $this->toMilliseconds($this->quantity());
    }

    /**
     * Convert \PHPUnit's reported test time (microseconds) to milliseconds.
     *
     * @param  float  $time
     * @return int
     */
    protected function toMilliseconds($time): int
    {
        return round($time * 1000);
    }

    /**
     * @return float
     */
    public function quantity(): float
    {
        return $this->quantity;
    }
}