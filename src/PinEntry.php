<?php

declare(strict_types=1);

namespace BitWasp\PinEntry;

use BitWasp\PinEntry\Assuan\Assuan;
use BitWasp\PinEntry\Exception\PinEntryException;
use BitWasp\PinEntry\PinValidation\PinValidatorInterface;
use BitWasp\PinEntry\Process\ProcessInterface;

class PinEntry
{
    /**
     * @var ProcessInterface
     */
    private $process;

    /**
     * @var Assuan
     */
    private $assuan;

    public function __construct(ProcessInterface $process, Assuan $assuan = null)
    {
        $response = $process->recv();
        if ($response !== "OK Pleased to meet you\n") {
            throw new PinEntryException("First message from pinentry did not match expected value");
        }
        $this->process = $process;
        $this->assuan = $assuan ?: new Assuan();
    }

    /**
     * @param string $key
     * @param string $value
     * @return Response
     * @throws Exception\RemotePinEntryException
     */
    public function setOption(string $key, string $value): Response
    {
        $this->assuan->send($this->process, Command::OPTION, "{$key} {$value}");
        $msg = $this->assuan->parseResponse($this->process);
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
        $response = $this->assuan->parseResponse($this->process);

        if (empty($response->getData())) {
            throw new \RuntimeException("expecting info in response");
        }
        list ($result) = $response->getData();

        return $result;
    }

    public function getPin(PinRequest $request, PinValidatorInterface $pinValidator): string
    {
        foreach ($request->getCommands() as $command => $param) {
            $this->assuan->send($this->process, $command, $param);
            $this->assuan->parseResponse($this->process);
        }

        $error = null;
        $pin = '';
        while (!$pinValidator->validate($pin, $error)) {
            if ($pin !== '') {
                $this->assuan->send($this->process, Command::SETERROR, $error);
                $this->assuan->parseResponse($this->process);
            }

            $this->assuan->send($this->process, Command::GETPIN);
            $response = $this->assuan->parseResponse($this->process);
            if (empty($response->getData())) {
                throw new \RuntimeException("expecting pin in response");
            }

            list ($pin) = $response->getData();
        }

        return $pin;
    }
}
