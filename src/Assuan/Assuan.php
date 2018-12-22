<?php

declare(strict_types=1);

namespace BitWasp\PinEntry\Assuan;

use BitWasp\PinEntry\Exception\RemotePinEntryException;
use BitWasp\PinEntry\Process\ProcessInterface;
use BitWasp\PinEntry\Response;

class Assuan
{
    public function parseResponse(ProcessInterface $process)
    {
        $data = [];
        $statuses = [];
        $comments = [];
        $okMsg = null;

        for (;;) {
            echo "call now\n";
            $buffer = $process->recv();
            var_dump("BUFFER", $buffer);
            foreach (explode("\n", $buffer) as $piece) {
                if (substr($piece, 0, 4) === "ERR ") {
                    $c = explode(" ", $piece, 3);
                    // don't change state, it's only a single line
                    throw new RemotePinEntryException($c[2], (int)$c[1]);
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

    public function send(ProcessInterface $process, string $command, string $params = null)
    {
        $process->send(sprintf(
            "%s%s",
            $command,
            $params ? " {$params}" : ""
        ));
    }
}
