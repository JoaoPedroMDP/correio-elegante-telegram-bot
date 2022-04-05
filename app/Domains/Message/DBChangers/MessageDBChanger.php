<?php
declare(strict_types=1);


namespace App\Domains\Message\DBChangers;


use App\Domains\Core\Interfaces\DBChangerInterface;

/**
 * Class MessageDBChanger
 * @package App\Domains\Message\DBChangers
 */
class MessageDBChanger implements DBChangerInterface
{
    /**
     * @var int
     */
    public $messageTid;

    /**
     * @var int
     */
    public $senderTid;

    /**
     * @var int
     */
    public $targetTid;

    /**
     * @var string
     */
    public $message;

    /**
     * MessageDBChanger constructor.
     * @param int $messageTid
     * @param int $senderTid
     * @param int $targetTid
     * @param string $message
     */
    public function __construct(int $messageTid, int $senderTid, int $targetTid, string $message)
    {
        $this->messageTid = $messageTid;
        $this->senderTid = $senderTid;
        $this->targetTid = $targetTid;
        $this->message = $message;
    }

    /**
     * @param array $data
     * @return MessageDBChanger
     */
    public static function fromArray(array $data): MessageDBChanger
    {
        return new self(
            $data['messageTid'],
            $data['senderTid'],
            $data['targetTid'],
            $data['message']
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "message_tid" => $this->messageTid,
            "sender_tid" => $this->senderTid,
            "target_tid" => $this->targetTid,
            "message" => $this->message,
        ];
    }
}
