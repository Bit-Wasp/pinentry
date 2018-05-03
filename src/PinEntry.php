<?php

declare(strict_types=1);

namespace BitWasp\PinEntry;

use BitWasp\PinEntry\Exception\PinEntryException;
use BitWasp\PinEntry\Process\ProcessInterface;

class PinEntry
{
    /**
     * @var ProcessInterface
     */
    private $process;

    public function __construct(
        ProcessInterface $process
    ) {
        $msg = $process->waitFor("OK");
        if ($msg !== "OK Pleased to meet you\n") {
            throw new PinEntryException("First message from pinentry did not match expected value");
        }
        $this->process = $process;
    }

    public function getInfo(string $type): string
    {
        $this->process->send(Command::GETINFO . " {$type}\n");
        $msg = $this->process->waitFor("D");
        return $msg;
    }

    public function getPin(PinRequest $request): string
    {
        foreach ($request->getCommands() as $command => $param) {
            $this->process->send("{$command} {$param}\n");
            $this->process->waitFor("OK");
        }

        foreach ($request->getOptions() as $option => $value) {
            $this->process->send(Command::OPTION . " {$option} {$value}\n");
            $this->process->waitFor("OK");
        }

        $this->process->send(Command::GETPIN . "\n");
        $msg = $this->process->waitFor("D");
        return $msg;
    }
}
