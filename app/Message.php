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
        'message_tid','sender_tid', 'target_tid', 'text'
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
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
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
