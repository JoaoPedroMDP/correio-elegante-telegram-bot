<?php
declare(strict_types=1);


namespace App\Domains\Message\Persistence;


use App\Message;

/**
 * Class MessageRepository
 * @package App\Domains\Message\Persistence
 */
class MessageRepository
{
    /**
     * @param string $text
     * @param string $senderChatId
     * @param string $targetChatId
     * @return Message
     */
    public function storeMessage(string $text, string $senderChatId, string $targetChatId): Message
    {
        $message = new Message;
        $message->setTargetChatId($targetChatId);
        $message->setSenderChatId($senderChatId);
        $message->setText($text);

        $message->save();
        return $message;
    }
}
