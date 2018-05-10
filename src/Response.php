<?php

declare(strict_types=1);

namespace BitWasp\PinEntry;

class Response
{
    /**
     * Ok Message is debugging information provided after the OK message.
     * @var null|string
     */
    private $okMsg;

    /**
     * Continuous data stream built up response start to OK
     * @var string[]
     */
    private $data;

    /**
     * Stream of statuses passed back in the response.
     * @var array
     */
    private $statuses;

    /**
     * List of comments returned in the response
     * @var string[]
     */
    private $comments = [];

    public function __construct(
        array $data = [],
        array $statuses = [],
        array $comments = [],
        string $okMsg = null
    ) {
        $this->data = $data;
        $this->statuses = $statuses;
        $this->comments = $comments;
        $this->okMsg = $okMsg;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getStatuses(): array
    {
        return $this->statuses;
    }

    public function getOkMsg()
    {
        return $this->okMsg;
    }
}
