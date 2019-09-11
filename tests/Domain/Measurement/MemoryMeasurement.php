<?php

namespace PhpunitMemoryAndTimeUsageListener\Domain\Measurement;

/**
 * Class MemoryMeasurement
 */
class MemoryMeasurement implements Measurement
{
    /**
     * @var float
     */
    private $quantity;

    /**
     * MemoryMeasurement constructor.
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
        return 'bytes';
    }

    /**
     * @return float
     */
    public function score(): float
    {
        return $this->toKiloBytes($this->quantity());
    }

    /**
     * @param $bytes
     * @return float
     */
    protected function toKiloBytes($bytes): float
    {
        return round($bytes / 1024, 2);
    }

    /**
     * @return float
     */
    public function quantity(): float
    {
        return $this->quantity;
    }
}