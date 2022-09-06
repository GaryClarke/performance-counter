<?php

namespace ClarkeTech\PerformanceCounter\Tests;

use ClarkeTech\PerformanceCounter\PerformanceCounter;
use PHPUnit\Framework\TestCase;

class PerformanceCounterTest extends TestCase
{
    private string $counterKey1 = 'test_counter1';
    private string $counterKey2 = 'test_counter2';
    private PerformanceCounter $unit;

    protected function setUp(): void
    {
        $this->unit = PerformanceCounter::getInstance();
        $this->unit->reset();
    }

    /** @test */
    public function average_process_time_can_be_obtained_for_multiple_keys(): void
    {
        $this->unit->timeIterationStart($this->counterKey1);

        usleep(random_int(100, 100000));

        for ($i = 1; $i <= 5; $i++) {
            $this->unit->timeIterationStart($this->counterKey2);
            usleep(random_int(100, 100000));
            $this->unit->timeIterationEnd($this->counterKey2);
        }

        $this->unit->timeIterationEnd($this->counterKey1);

        $this->assertGreaterThan(10, $this->unit->getAverageIterationTime($this->counterKey1));
        $this->assertLessThan(100, $this->unit->getAverageIterationTime($this->counterKey2));
    }

    /** @test */
    public function a_key_can_be_cleared(): void
    {
        $this->unit->timeIterationStart($this->counterKey1);

        $this->unit->timeIterationStart($this->counterKey2);

        $this->unit->clearKey($this->counterKey2);

        $this->assertContains($this->counterKey1, $this->unit->getKeys());
        $this->assertNotContains($this->counterKey2, $this->unit->getKeys());
    }

    /** @test */
    public function the_counter_can_be_reset(): void
    {
        $this->unit->timeIterationStart($this->counterKey1);

        $this->unit->timeIterationStart($this->counterKey2);

        $this->unit->reset();

        $this->assertEmpty($this->unit->getKeys());
    }
}