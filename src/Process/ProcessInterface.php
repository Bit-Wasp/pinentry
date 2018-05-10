<?php

declare(strict_types=1);

namespace BitWasp\PinEntry\Process;

use BitWasp\PinEntry\Response;

interface ProcessInterface
{
    /**
     * Sends a string of data to the process. Returns the number
     * of bytes written.
     * @param string $data
     * @return int
     */
    public function send(string $data);
    public function recv(): Response;
    public function close(): bool;
}
