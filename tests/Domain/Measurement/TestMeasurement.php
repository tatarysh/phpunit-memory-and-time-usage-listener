<?php

namespace PhpunitMemoryAndTimeUsageListener\Domain\Measurement;

/**
 * Class TestMeasurement
 */
class TestMeasurement
{
    /**
     * @var
     */
    private $testName;

    /**
     * @var
     */
    private $testFile;

    /**
     * @var TimeMeasurement
     */
    private $timeUsage;

    /**
     * @var MemoryMeasurement
     */
    private $memoryUsage;

    /**
     * @var MemoryMeasurement
     */
    private $memoryPeakDifference;

    /**
     * TestMeasurement constructor.
     *
     * @param  string  $name
     * @param  string  $file
     * @param  TimeMeasurement  $timeUsage
     * @param  MemoryMeasurement  $memoryUsage
     * @param  MemoryMeasurement  $memoryPeakUsage
     */
    public function __construct(
        string $name,
        string $file,
        TimeMeasurement $timeUsage,
        MemoryMeasurement $memoryUsage,
        MemoryMeasurement $memoryPeakUsage
    ) {
        $this->testName = $name;
        $this->testFile = $file;
        $this->timeUsage = $timeUsage;
        $this->memoryUsage = $memoryUsage;
        $this->memoryPeakDifference = $memoryPeakUsage;
    }

    /**
     * @return string
     */
    public function measuredInformationMessage(): string
    {
        return sprintf('%s in file %s measurements: %s milliseconds, %sKb memory usage, %sKb memory peak difference',
            $this->testName, $this->testFile, $this->timeUsage->score(),
            $this->memoryUsage->score(), $this->memoryPeakDifference->score());
    }
}