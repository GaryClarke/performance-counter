<?php

namespace ClarkeTech\PerformanceCounter;

/**
 * Calculates the average iteration time for a given process
 *
 * Keys can be nested which enables you to measure the performance of inner and outer loops.
 * Designed to be used as a development utility tool. Recommended to be removed from code after use.
 *
 * @see PerformanceCounterTest::average_process_time_can_be_obtained_for_multiple_keys for a demo
 * of how this works
 *
 * @author Gary Clarke <info@garyclarke.tech>
 */
final class PerformanceCounter
{
    
    private static ?self $instance = null;
    private array $start = [];
    private array $iterationCount = [];
    private array $totalElapsedTime = [];
    private array $averageIterationTime = [];

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Capture the start time for one iteration for a given key
     */
    public function timeIterationStart($key): void
    {
        if (!isset($this->iterationCount[$key])) {
            $this->iterationCount[$key] = 0;
            $this->totalElapsedTime[$key] = 0;
        }

        $this->iterationCount[$key] ++;

        $this->start[$key] = microtime(true);
    }

    /**
     * Capture the end time for one iteration for a given key
     */
    public function timeIterationEnd($key): void
    {
        $endTime = microtime(true);

        $this->totalElapsedTime[$key] += round($endTime - $this->start[$key], 3) * 1000;

        $this->averageIterationTime[$key] = $this->totalElapsedTime[$key] / max($this->iterationCount[$key], 1);
    }

    public function getAverageIterationTime($key)
    {
        return $this->averageIterationTime[$key];
    }

    public function clearKey($key): void
    {
        unset(
            $this->start[$key],
            $this->iterationCount[$key],
            $this->totalElapsedTime[$key],
            $this->averageIterationTime[$key]
        );
    }

    public function reset(): void
    {
        $this->start = [];
        $this->iterationCount = [];
        $this->totalElapsedTime = [];
        $this->averageIterationTime = [];
    }

    public function getKeys(): array
    {
        return array_keys($this->iterationCount);
    }
}