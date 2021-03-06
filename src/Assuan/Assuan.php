<?php

declare(strict_types=1);

namespace BitWasp\PinEntry\Assuan;

use BitWasp\PinEntry\Exception\RemotePinEntryException;
use BitWasp\PinEntry\Process\ProcessInterface;
use BitWasp\PinEntry\Response;

class Assuan
{
    /**
     * @param ProcessInterface $process
     * @return Response
     * @throws RemotePinEntryException
     */
    public function parseResponse(ProcessInterface $process): Response
    {
        $data = [];
        $statuses = [];
        $comments = [];
        $okMsg = null;

        for (;;) {
            $buffer = $process->recv();
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

    /**
     * @param ProcessInterface $process
     * @param string $command
     * @param string|null $params
     */
    public function send(ProcessInterface $process, string $command, string $params = null)
    {
        $process->send(sprintf("%s%s\n", $command, $params ? " {$params}" : ""));
    }
}
