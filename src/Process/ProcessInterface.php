<?php

declare(strict_types=1);

namespace BitWasp\PinEntry\Process;

interface ProcessInterface
{
    /**
     * Sends a string of data to the process. Returns the number
     * of bytes written.
     * @param string $data
     * @return int
     */
    public function send(string $data);
    public function recv(): string;
    public function close(): bool;
}
