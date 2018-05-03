<?php

declare(strict_types=1);

namespace BitWasp\PinEntry;

class Request
{
    /**
     * @var string[]|int[]
     */
    private $commands = [];

    /**
     * @var string[]|int[]
     */
    private $options = [];

    public function withOption(string $key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    public function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options);
    }

    public function getOption(string $key)
    {
        if ($this->hasOption($key)) {
            return $this->options[$key];
        }
        return null;
    }

    /**
     * @return string[]|int[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return string[]|int[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    private function withCommand(string $command, $param)
    {
        $this->commands[$command] = $param;
    }

    private function hasCommand(string $command): bool
    {
        return array_key_exists($command, $this->commands);
    }

    private function getCommand(string $command)
    {
        if (!$this->hasCommand($command)) {
            return null;
        }
        return $this->commands[$command];
    }

    public function withDesc(string $desc)
    {
        $this->withCommand(Command::SETDESC, $desc);
        return $this;
    }

    public function hasDesc(): bool
    {
        return $this->hasCommand(Command::SETDESC);
    }

    public function getDesc()
    {
        return $this->getCommand(Command::SETDESC);
    }

    public function withPrompt(string $desc)
    {
        $this->withCommand(Command::SETPROMPT, $desc);
        return $this;
    }

    public function hasPrompt(): bool
    {
        return $this->hasCommand(Command::SETPROMPT);
    }

    public function getPrompt()
    {
        return $this->getCommand(Command::SETPROMPT);
    }

    public function withKeyInfo(string $keyInfo)
    {
        $this->withCommand(Command::SETKEYINFO, $keyInfo);
        return $this;
    }

    public function hasKeyInfo(): bool
    {
        return $this->hasCommand(Command::SETKEYINFO);
    }

    public function getKeyInfo()
    {
        return $this->getCommand(Command::SETKEYINFO);
    }

    public function withRepeat($repeat)
    {
        $this->withCommand(Command::SETREPEAT, $repeat);
        return $this;
    }

    public function hasRepeat(): bool
    {
        return $this->hasCommand(Command::SETREPEAT);
    }

    public function getRepeat()
    {
        return $this->getCommand(Command::SETREPEAT);
    }

    /**
     * @param string $repeatError
     * @return $this
     */
    public function withRepeatError(string $repeatError)
    {
        $this->withCommand(Command::SETREPEATERROR, $repeatError);
        return $this;
    }

    public function hasRepeatError(): bool
    {
        return $this->hasCommand(Command::SETREPEATERROR);
    }

    public function getRepeatError()
    {
        return $this->getCommand(Command::SETREPEATERROR);
    }

    public function withError(string $error)
    {
        $this->withCommand(Command::SETERROR, $error);
        return $this;
    }

    public function hasError(): bool
    {
        return $this->hasCommand(Command::SETERROR);
    }

    public function getError()
    {
        return $this->getCommand(Command::SETERROR);
    }

    public function withOkButton(string $ok)
    {
        $this->withCommand(Command::SETOK, $ok);
        return $this;
    }

    public function hasOkButton(): bool
    {
        return $this->hasCommand(Command::SETOK);
    }

    public function getOkButton()
    {
        return $this->getCommand(Command::SETOK);
    }

    public function withNotOk(string $ok)
    {
        $this->withCommand(Command::SETNOTOK, $ok);
        return $this;
    }

    public function hasNotOk(): bool
    {
        return $this->hasCommand(Command::SETNOTOK);
    }

    public function getNotOk()
    {
        return $this->getCommand(Command::SETNOTOK);
    }

    public function withCancelButton(string $cancel)
    {
        $this->withCommand(Command::SETCANCEL, $cancel);
        return $this;
    }

    public function hasCancelButton(): bool
    {
        return $this->hasCommand(Command::SETCANCEL);
    }

    public function getCancelButton()
    {
        return $this->getCommand(Command::SETCANCEL);
    }

    public function withTitle(string $title)
    {
        $this->withCommand(Command::SETTITLE, $title);
        return $this;
    }

    public function hasTitle(): bool
    {
        return $this->hasCommand(Command::SETTITLE);
    }

    public function getTitle()
    {
        return $this->getCommand(Command::SETTITLE);
    }

    public function withQualityBar(string $qualityBar)
    {
        $this->withCommand(Command::SETQUALITYBAR, $qualityBar);
        return $this;
    }

    public function hasQualityBar(): bool
    {
        return $this->hasCommand(Command::SETQUALITYBAR);
    }

    public function getQualityBar()
    {
        return $this->getCommand(Command::SETQUALITYBAR);
    }

    public function withQualityBarTooltip(string $tooltip)
    {
        $this->withCommand(Command::SETQUALITYBAR_TT, $tooltip);
        return $this;
    }

    public function hasQualityBarTooltip(): bool
    {
        return $this->hasCommand(Command::SETQUALITYBAR_TT);
    }

    public function getQualityBarTooltip()
    {
        return $this->getCommand(Command::SETQUALITYBAR_TT);
    }

    public function withTimeout(int $timeout)
    {
        $this->withCommand(Command::SETTIMEOUT, $timeout);
        return $this;
    }

    public function hasTimeout(): bool
    {
        return $this->hasCommand(Command::SETTIMEOUT);
    }

    public function getTimeout()
    {
        return $this->getCommand(Command::SETTIMEOUT);
    }
}
