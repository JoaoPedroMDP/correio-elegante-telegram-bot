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
     * @param int $senderChatId
     * @param int $targetChatId
     * @return Message
     */
    public function storeMessage(string $text, int $senderChatId, int $targetChatId): Message
    {
        $message = new Message;
        $message->setTargetTid($targetChatId);
        $message->setSenderTid($senderChatId);
        $message->setText($text);

        $message->save();
        return $message;
    }
}
