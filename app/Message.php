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
    protected $fillable= ['sender_chat_id', 'target_chat_id', 'text'];

    /**
     * @return string
     */
    public function getSenderChatId(): string
    {
        return $this->sender_chat_id;
    }

    /**
     * @param string $sender_chat_id
     */
    public function setSenderChatId(string $sender_chat_id): void
    {
        $this->sender_chat_id = $sender_chat_id;
    }

    /**
     * @return string
     */
    public function getTargetChatId(): string
    {
        return $this->target_chat_id;
    }

    /**
     * @param string $target_chat_id
     */
    public function setTargetChatId(string $target_chat_id): void
    {
        $this->target_chat_id = $target_chat_id;
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

}
