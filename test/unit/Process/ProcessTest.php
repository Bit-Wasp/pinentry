<?php

declare(strict_types=1);

namespace BitWasp\Test\Process;

use BitWasp\PinEntry\Process\Process;
use BitWasp\Test\PinEntry\TestCase;

class ProcessTest extends TestCase
{
    public function testStartsAndStopsProcess()
    {
        $executable = "/usr/bin/pinentry";
        $process = new Process($executable);
        $status = $process->getStatus();
        $this->assertTrue(file_exists("/proc/{$status['pid']}"));
        $process->close();
        $this->assertFalse(file_exists("/proc/{$status['pid']}"));
    }
}
