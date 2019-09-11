<?php

namespace PhpunitMemoryAndTimeUsageListener\Listener\Measurement;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;
use PhpunitMemoryAndTimeUsageListener\Domain\Measurement\MemoryMeasurement;
use PhpunitMemoryAndTimeUsageListener\Domain\Measurement\TestMeasurement;
use PhpunitMemoryAndTimeUsageListener\Domain\Measurement\TimeMeasurement;

/**
 * Class TimeAndMemoryTestListener
 */
class TimeAndMemoryTestListener implements TestListener
{
    use TestListenerDefaultImplementation;

    /**
     * @var int
     */
    protected $testSuitesRunning = 0;

    /**
     * @var array
     */
    protected $testMeasurementCollection = [];

    /**
     * @var bool
     */
    protected $showOnlyIfEdgeIsExceeded = false;

    /**
     * Time in milliseconds we consider a test has a need to see if a refactor is needed
     *
     * @var int
     */
    protected $executionTimeEdge = 100;

    /**
     * Memory bytes usage we consider a test has a need to see if a refactor is needed
     *
     * @var int
     */
    protected $memoryUsageEdge = 1024;

    /**
     * Memory bytes usage we consider a test has a need to see if a refactor is needed
     *
     * @var int
     */
    protected $memoryPeakDifferenceEdge = 1024;

    /**
     * @var TimeMeasurement
     */
    protected $executionTime;

    /**
     * @var int
     */
    protected $memoryUsage;

    /**
     * @var int
     */
    protected $memoryPeakIncrease;

    /**
     * TimeAndMemoryTestListener constructor.
     *
     * @param  array  $configurationOptions
     */
    public function __construct($configurationOptions = [])
    {
        $this->setConfigurationOptions($configurationOptions);
    }

    /**
     * @param $configurationOptions
     */
    protected function setConfigurationOptions($configurationOptions): void
    {
        if ($showOnlyIfEdgeIsExceeded = $configurationOptions['showOnlyIfEdgeIsExceeded'] ?? null) {
            $this->showOnlyIfEdgeIsExceeded = $showOnlyIfEdgeIsExceeded;
        }

        if ($executionTimeEdge = $configurationOptions['executionTimeEdge'] ?? null) {
            $this->executionTimeEdge = $executionTimeEdge;
        }

        if ($memoryUsageEdge = $configurationOptions['memoryUsageEdge'] ?? null) {
            $this->memoryUsageEdge = $memoryUsageEdge;
        }

        if ($memoryPeakDifferenceEdge = $configurationOptions['memoryPeakDifferenceEdge'] ?? null) {
            $this->memoryPeakDifferenceEdge = $memoryPeakDifferenceEdge;
        }
    }

    /**
     * @param  Test  $test
     */
    public function startTest(Test $test): void
    {
        $this->memoryUsage = memory_get_usage();
        $this->memoryPeakIncrease = memory_get_peak_usage();
    }

    /**
     * @param  Test  $test
     * @param $time
     */
    public function endTest(Test $test, float $time): void
    {
        $this->executionTime = new TimeMeasurement($time);
        $this->memoryUsage = memory_get_usage() - $this->memoryUsage;
        $this->memoryPeakIncrease = memory_get_peak_usage() - $this->memoryPeakIncrease;

        if ($this->haveToSaveTestMeasurement()) {
            $this->saveTestMeasurement($test);
        }
    }

    /**
     * @return bool
     */
    protected function haveToSaveTestMeasurement(): bool
    {
        if (!$this->showOnlyIfEdgeIsExceeded) {
            return true;
        }

        return $this->isAPotentialCriticalTimeUsage() || $this->isAPotentialCriticalMemoryUsage() || $this->isAPotentialCriticalMemoryPeakUsage();
    }

    /**
     * Check if test execution time is critical so we need to check it out
     *
     * @return bool
     */
    protected function isAPotentialCriticalTimeUsage(): bool
    {
        return $this->checkEdgeIsOverTaken($this->executionTime->score(), $this->executionTimeEdge);
    }

    /**
     * @param $value
     * @param $edgeValue
     * @return bool
     */
    protected function checkEdgeIsOverTaken($value, $edgeValue): bool
    {
        return $value >= $edgeValue;
    }

    /**
     * Check if test execution memory usage is critical so we need to check it out
     *
     * @return bool
     */
    protected function isAPotentialCriticalMemoryUsage(): bool
    {
        return $this->checkEdgeIsOverTaken($this->memoryUsage, $this->memoryUsageEdge);
    }

    /**
     * Check if test execution memory peak usage is critical so we need to check it out
     *
     * @return bool
     */
    protected function isAPotentialCriticalMemoryPeakUsage(): bool
    {
        return $this->checkEdgeIsOverTaken($this->memoryPeakIncrease, $this->memoryPeakDifferenceEdge);
    }

    /**
     * @param  Test  $test
     */
    private function saveTestMeasurement(Test $test): void
    {
        $name = ($test instanceof TestCase) ? $test->getName() : '';
        $this->testMeasurementCollection[] = new TestMeasurement(
            $name,
            get_class($test),
            $this->executionTime,
            new MemoryMeasurement($this->memoryUsage),
            new MemoryMeasurement($this->memoryPeakIncrease)
        );
    }

    /**
     * @param  TestSuite  $suite
     */
    public function startTestSuite(TestSuite $suite): void
    {
        $this->testSuitesRunning++;
    }

    /**
     * @param  TestSuite  $suite
     */
    public function endTestSuite(TestSuite $suite): void
    {
        $this->testSuitesRunning--;

        if ($this->testSuitesRunning !== 0 || count($this->testMeasurementCollection) <= 0) {
            return;
        }

        echo PHP_EOL.'Time & Memory measurement results: '.PHP_EOL;

        foreach ($this->testMeasurementCollection as $key => $testMeasurement) {
            /** @var TestMeasurement $testMeasurement */
            echo PHP_EOL.($key + 1).' - '.$testMeasurement->measuredInformationMessage();
        }
    }
}
