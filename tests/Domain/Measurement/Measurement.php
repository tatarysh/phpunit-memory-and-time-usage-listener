<?php

namespace PhpunitMemoryAndTimeUsageListener\Domain\Measurement;

/**
 * Interface Measurement
 */
interface Measurement
{
    /**
     * @return float
     */
    public function quantity(): float;

    /**
     * @return string
     */
    public function unit(): string;

    /**
     * @return float
     */
    public function score(): float;
}