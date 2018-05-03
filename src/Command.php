<?php

declare(strict_types=1);

namespace BitWasp\PinEntry;

class Command
{
    const SETDESC = 'SETDESC';
    const SETPROMPT = 'SETPROMPT';
    const SETKEYINFO = 'SETKEYINFO';
    const SETREPEAT = 'SETREPEAT';
    const SETTITLE = 'SETTITLE';
    const SETERROR = 'SETERROR';
    const SETOK = 'SETOK';
    const SETNOTOK = 'SETNOTOK';
    const SETCANCEL = 'SETCANCEL';
    const SETREPEATERROR = 'SETREPEATERROR';
    const SETTIMEOUT = 'SETTIMEOUT';
    const SETQUALITYBAR = 'SETQUALITYBAR';
    const SETQUALITYBAR_TT = 'SETQUALITYBAR_TT';

    private $cmd;
    private $param;

    public function __construct(string $command, $param)
    {
        $this->cmd = $command;
        $this->param = $param;
    }

    public function getCommand(): string
    {
        return $this->cmd;
    }

    public function getParam()
    {
        return $this->param;
    }
}
