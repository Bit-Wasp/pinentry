<?php

declare(strict_types=1);

namespace BitWasp\PinEntry\Process;

use BitWasp\PinEntry\Exception;

class Process implements ProcessInterface
{
    /**
     * @var resource A 'process' resource
     */
    private $process;

    /**
     * @var IPC -
     */
    private $ipc;

    /**
     * @var bool
     */
    private $running = true;

    public function __construct(string $executable, array $overrideDescriptors = [])
    {
        $pipes = [];
        $process = proc_open($executable, IPC::buildDescriptors($overrideDescriptors), $pipes);
        if (!(is_resource($process) && get_resource_type($process) === "process")) {
            throw new Exception\PinEntryException("Failed to start process");
        }

        $this->ipc = new IPC($pipes);
        $this->process = $process;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close(): bool
    {
        if ($this->running) {
            $this->stopRunning();
        }
        return true;
    }

    /**
     * Called during shutdown routine. Per documentation,
     * file handles should be closed before we call proc_close
     */
    private function stopRunning()
    {
        $this->ipc->close();
        proc_close($this->process);
        $this->running = false;
    }

    public function getStatus(): array
    {
        return proc_get_status($this->process);
    }

    public function send(string $data)
    {
        $this->ipc->send($data);
    }

    public function recv(): string
    {
        return $this->ipc->readStdOut();
    }
}
