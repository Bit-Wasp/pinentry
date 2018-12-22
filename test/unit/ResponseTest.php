<?php
declare(strict_types=1);

namespace BitWasp\Test\PinEntry;

use BitWasp\PinEntry\Response;

class ResponseTest extends TestCase
{
    public function getResponseData(): array
    {
        return [
            [
                ["data1", "data2"], [], [], null,
            ],
            [
                [], ["status1", "status2"], [], null,
            ],
            [
                [], [], ["comment1", "comment2"], null,
            ],
            [
                [], [], [], "This is the OK message",
            ],
            [
                ['data1'], ['status1'], ['comment1'], "ok message",
            ],
        ];
    }

    /**
     * @param array $data
     * @param array $statuses
     * @param array $comments
     * @param string|null $okMsg
     * @dataProvider getResponseData
     */
    public function testOkMsg(array $data, array $statuses, array $comments, string $okMsg = null)
    {
        $response = new Response($data, $statuses, $comments, $okMsg);
        $this->assertEquals($data, $response->getData());
        $this->assertEquals($statuses, $response->getStatuses());
        $this->assertEquals($comments, $response->getComments());
        $this->assertEquals($okMsg, $response->getOkMsg());
    }
}
