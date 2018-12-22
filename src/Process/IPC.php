<?php

declare(strict_types=1);

namespace BitWasp\PinEntry\Process;

class IPC
{
    const STDIN = 0;
    const STDOUT = 1;
    const STDERR = 2;

    /**
     * Contains a default file descriptor definitions
     * for the STDIN, STDOUT, STDERR streams.
     * @var array
     */
    protected static $defaultDescriptor = [
        // Define the STDIN pipe the child will read from
        self::STDIN => ["pipe", "r"],

        // Define the STDOUT pipe the child will write to
        self::STDOUT => ["pipe", "w"],

        // Define the STDERR pipe the child will write to. Can also
        // specify a file instead of writing to this process.
        self::STDERR => ["pipe", "w"],
    ];

    /**
     * @var resource[]
     */
    private $fhList = [];

    public function __construct(array $fileHandles)
    {
        $missingKeys = array_diff_key(self::$defaultDescriptor, $fileHandles);
        if (count($missingKeys) > 0) {
            throw new \InvalidArgumentException("Missing required file handle ({$missingKeys[0]})");
        }

        foreach ($fileHandles as $fhKey => $fh) {
            if (!(is_resource($fh) && get_resource_type($fh) === "stream")) {
                throw new \InvalidArgumentException("Invalid file handle ({$fhKey})");
            }
        }

        stream_set_blocking($fileHandles[self::STDOUT], false);
        stream_set_blocking($fileHandles[self::STDERR], false);

        $this->fhList = $fileHandles;
    }

    public function close()
    {
        fclose($this->fhList[self::STDIN]);
        fclose($this->fhList[self::STDOUT]);
        fclose($this->fhList[self::STDERR]);
    }

    /**
     * @param string $data
     * @return int
     */
    public function send(string $data): int
    {
        $write = fwrite($this->fhList[self::STDIN], $data);
        if ($write === false) {
            throw new \RuntimeException("Failed to write to process stdin");
        }
        return $write;
    }

    public function readStdOut(): string
    {
        return $this->blockingRead($this->fhList[self::STDOUT]);
    }

    /**
     * @param resource $fh
     * @return string
     */
    private function blockingRead($fh)
    {
        $rx = [$fh];
        $wx = [];
        $ex = [];
        // This will pause execution until the stream changes state,
        // most likely indicating it is ready to be read.
        if (false === stream_select($rx, $wx, $ex, null, 0)) {
            throw new \RuntimeException("stream_select failed");
        }

        // maybe we should inspect $rx to see what the new status is before reading
        $buffer = stream_get_contents($fh);
        if ($buffer === false) {
            throw new \RuntimeException("Reading from stream failed");
        }

        assert($buffer !== "");

        return $buffer;
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
