<?php
declare(strict_types=1);


namespace App\Domains\Message\Persistence;


use App\Domains\Message\DBChangers\MessageDBChanger;
use App\Message;

/**
 * Class MessageRepository
 * @package App\Domains\Message\Persistence
 */
class MessageRepository
{
    /**
     * @param MessageDBChanger $changer
     * @return Message
     */
    public function storeMessage(MessageDBChanger $changer): Message
    {
        $message = new Message;
        $message->fill($changer->toArray());
        $message->save();
        return $message;
    }

    /**
     * @param int $tid
     * @return Message|null
     */
    public function getMessageByTid(int $tid): ?Message
    {
        return Message::where('message_tid', '=', $tid)->first();
    }
}
