<?php

declare(strict_types=1);

namespace BitWasp\PinEntry\Process;

interface ProcessInterface
{
    public function send(string $data);

    public function waitFor(string $text);

    public function recv();
    public function close(): bool;
}
