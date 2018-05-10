<?php

declare(strict_types=1);

namespace BitWasp\PinEntry;

use BitWasp\PinEntry\Exception\PinEntryException;
use BitWasp\PinEntry\PinValidation\PinValidatorInterface;
use BitWasp\PinEntry\Process\ProcessInterface;

class PinEntry
{
    /**
     * @var ProcessInterface
     */
    private $process;

    public function __construct(ProcessInterface $process) {
        $response = $process->recv();
        if ($response->getOkMsg() !== "Pleased to meet you") {
            throw new PinEntryException("First message from pinentry did not match expected value");
        }
        $this->process = $process;
    }

    /**
     * @param string $key
     * @param int|string $value
     * @return mixed
     */
    public function setOption(string $key, $value): Response
    {
        $this->process->send(Command::OPTION . " {$key} {$value}\n");
        $msg = $this->process->recv();
        return $msg;
    }

    public function getPID(): int
    {
        return (int) $this->getInfo('pid');
    }

    public function getVersion(): string
    {
        return $this->getInfo('version');
    }

    public function getInfo(string $type): string
    {
        $this->process->send(Command::GETINFO . " {$type}\n");
        $response = $this->process->recv();

        if (empty($response->getData())) {
            throw new \RuntimeException("expecting info in response");
        }
        list ($result) = $response->getData();

        return $result;
    }

    public function getPin(PinRequest $request, PinValidatorInterface $pinValidator): string
    {
        foreach ($request->getCommands() as $command => $param) {
            $this->process->send("{$command} {$param}\n");
            $this->process->recv();
        }

        $error = null;
        $pin = '';
        while (!$pinValidator->validate($pin, $error)) {
            if ($pin !== '') {
                $this->process->send(Command::SETERROR . " {$error}\n");
                $this->process->recv();
            }

            $this->process->send(Command::GETPIN . "\n");
            $response = $this->process->recv();
            if (empty($response->getData())) {
                throw new \RuntimeException("expecting pin in response");
            }

            list ($pin) = $response->getData();
        }

        return $pin;
    }
}
