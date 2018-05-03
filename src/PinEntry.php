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
            throw new PinEntryException("Unexpected start of pinnetry protocol");
        }
        $this->process = $process;
    }

    public function getInfo(string $type): string
    {
        $this->process->send("GETINFO {$type}\n");
        $msg = $this->process->waitFor("D");
        return $msg;
    }

    public function getPin(Request $request): string
    {
        foreach ($request->getCommands() as $command => $param) {
            $this->process->send("{$command} {$param}\n");
            $this->process->waitFor("OK");
        }

        foreach ($request->getOptions() as $option => $value) {
            $this->process->send("OPTION {$option} {$value}\n");
            $this->process->waitFor("OK");
        }

        $this->process->send("GETPIN\n");
        $msg = $this->process->waitFor("D");
        return $msg;
    }
}
