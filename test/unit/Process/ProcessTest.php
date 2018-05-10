<?php

declare(strict_types=1);

namespace BitWasp\Test\Process;

use BitWasp\PinEntry\Process\Process;
use BitWasp\Test\PinEntry\TestCase;

class ProcessTest extends TestCase
{
    public function getDescriptorFixtures(): array
    {
        return [
            [
                [
                    0 => ["pipe", "r"],
                    1 => ["pipe", "w"],
                    2 => ["pipe", "w"],
                ],
                []
            ],
            [
                [
                    0 => ["pipe", "r"],
                    1 => ["pipe", "w"],
                    2 => sys_get_temp_dir() . "/pinentrytest.err.log",
                ],
                [
                    2 => sys_get_temp_dir() . "/pinentrytest.err.log",
                ]
            ]
        ];
    }

    /**
     * @dataProvider getDescriptorFixtures
     * @param array $expected
     */
    public function testDefaultBuildDescriptors(array $expected, array $override)
    {
        $desc = Process::buildDescriptors($override);
        $this->assertInternalType('array', $desc);
        $this->assertCount(3, $desc);
        $this->assertEquals($expected, $desc);
    }
}
