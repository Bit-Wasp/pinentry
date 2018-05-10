<?php

declare(strict_types=1);

namespace BitWasp\PinEntry\Process;

use BitWasp\PinEntry\Exception;
use BitWasp\PinEntry\Response;

class Process implements ProcessInterface
{
    /**
     * Contains a default file descriptor definitions
     * for the STDIN, STDOUT, STDERR streams.
     * @var array
     */
    protected static $defaultDescriptor = [
        // Define the STDIN pipe the child will read from
        0 => ["pipe", "r"],

        // Define the STDOUT pipe the child will write to
        1 => ["pipe", "w"],

        // Define the STDERR pipe the child will write to. Can also
        // specify a file instead of writing to this process.
        2 => ["pipe", "w"],
    ];

    /**
     * @var resource A 'process' resource
     */
    private $process;

    /**
     * @var bool
     */
    private $running = true;

    /**
     * @var resource A 'stream' resource
     */
    private $stdout;

    /**
     * @var resource A 'stream' resource
     */
    private $stdin;

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
        $this->stdin = $pipes[0];
        $this->stdout = $pipes[1];
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
        fclose($this->stdin);
        fclose($this->stdout);
        proc_close($this->process);
    }

    public function send(string $data)
    {
        if (fwrite($this->stdin, $data) === false) {
            throw new \RuntimeException("Failed to write to process stdin");
        }
    }

    private function awaitResponse(): string
    {
        $rx = [$this->stdout];
        $wx = [];
        $ex = [];
        // This will pause execution until the stream changes state,
        // most likely indicating it is ready to be read.
        if (false === stream_select($rx, $wx, $ex, null, 0)) {
            throw new \RuntimeException("stream_select failed");
        }

        // maybe we should inspect $rx to see what the new status is before reading

        $buffer = stream_get_contents($this->stdout);
        assert($buffer !== "");

        return $buffer;
    }

    public function recv(): Response
    {
        $data = [];
        $statuses = [];
        $comments = [];
        $okMsg = null;

        for (;;) {
            $buffer = $this->awaitResponse();
            foreach (explode("\n", $buffer) as $piece) {
                if (substr($piece, 0, 4) === "ERR ") {
                    $c = explode(" ", $piece, 3);
                    // don't change state, it's only a single line
                    throw new Exception\RemotePinEntryException($c[2], (int)$c[1]);
                }

                $prefix = substr($piece, 0, 2);
                if ($prefix === "D ") {
                    $data[] = substr($piece, 2);
                } else if ($prefix === "S ") {
                    $statuses[] = substr($piece, 2);
                } else if ($prefix === "# ") {
                    // don't change state, it's only a single line
                    $comments[] = substr($piece, 2);
                } else if ($prefix === "OK") {
                    if (strlen($piece) > 2) {
                        $okMsg = substr($piece, 3);
                    }
                    break 2;
                }
            }
        }

        return new Response($data, $statuses, $comments, $okMsg);
    }


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
        foreach (array_intersect_key($descriptor, $overrideDescriptors) as $key => $value) {
            $descriptor[$key] = $overrideDescriptors[$key];
        }
        return $descriptor;
    }

}
