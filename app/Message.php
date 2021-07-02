<?php
declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * @package App
 */
class Message extends Model
{
    /**
     * @var string[]
     */
    protected $fillable= [
        'message_tid', 'sender_tid', 'target_tid', 'message'
    ];

    /**
     * @return int
     */
    public function getSenderTid(): int
    {
        return $this->sender_tid;
    }

    /**
     * @param int $sender_tid
     */
    public function setSenderTid(int $sender_tid): void
    {
        $this->sender_tid = $sender_tid;
    }

    /**
     * @return int
     */
    public function getTargetTid(): int
    {
        return $this->target_tid;
    }

    /**
     * @param int $target_tid
     */
    public function setTargetTid(int $target_tid): void
    {
        $this->target_tid = $target_tid;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string
     */
    public function setMessage(string $message): void
    {
        $this->text = $message;
    }

    /**
     * @return int
     */
    public function getMessageTid(): int
    {
        return $this->messageTid;
    }

    /**
     * @param int $messageTid
     */
    public function setMessageTid(int $messageTid): void
    {
        $this->messageTid = $messageTid;
    }

}
