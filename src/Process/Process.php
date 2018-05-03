<?php

declare(strict_types=1);

namespace BitWasp\PinEntry\Process;

use BitWasp\PinEntry\Exception;
use GuzzleHttp\Stream\Stream;

class Process implements ProcessInterface
{
    /**
     * @var array
     */
    protected static $defaultDescriptor = [
        0 => ["pipe", "r"], // STDIN pipe the child will read from
        1 => ["pipe", "w"], // STDOUT pipe the child will write to
        2 => ["pipe", "w"], // STDERR pipe the child to write to
    ];

    /**
     * @var resource
     */
    private $process;

    /**
     * @var bool
     */
    private $running = true;

    /**
     * @var Stream
     */
    private $stdout;

    /**
     * @var Stream
     */
    private $stdin;

    /**
     * Return the default descriptors values, overloaded with
     * the value in $overrideDescriptors if the same key is set there.
     *
     * @param array[] $overrideDescriptors
     * @return array[]
     */
    public static function buildDescriptors(array $overrideDescriptors = []): array
    {
        $descriptor = static::$defaultDescriptor;
        foreach (array_keys($descriptor) as $key) {
            if (array_key_exists($key, $overrideDescriptors)) {
                $descriptor[$key] = $overrideDescriptors[$key];
            }
        }
        return $descriptor;
    }

    public function __construct(
        string $executable,
        array $overrideDescriptors = []
    ) {
        $process = proc_open($executable, self::buildDescriptors($overrideDescriptors), $pipes);
        if (!is_resource($process)) {
            throw new Exception\PinEntryException("Failed to start pinentry");
        }

        stream_set_blocking($pipes[1], false);

        $this->process = $process;
        $this->stdin = Stream::factory($pipes[0]);
        $this->stdout = Stream::factory($pipes[1]);
    }

    /**
     * Called during shutdown routine. Per documentation,
     * file handles should be closed before we call proc_close
     */
    private function stopRunning()
    {
        $this->stdin->close();
        $this->stdout->close();
        proc_close($this->process);
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

    public function send(string $data)
    {
        return $this->stdin->write($data);
    }

    public function waitFor(string $text)
    {
        $textLen = strlen($text);
        for (;;) {
            $msg = $this->recv();
            if ("" === $msg) {
                usleep(1000);
            } else if (substr($msg, 0, $textLen) === $text) {
                return $msg;
            } else {
                if (substr($msg, 0, 3) === "ERR") {
                    list (, $code, $error) = explode(" ", $msg, 3);
                    throw new Exception\RemotePinEntryException($error, (int) $code);
                }
                throw new Exception\UnexpectedResponseException($msg);
            }
        }
    }

    public function recv(): string
    {
        return $this->stdout->getContents();
    }
}
