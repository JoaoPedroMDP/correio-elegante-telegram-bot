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
     * @param string $senderUsername
     * @param string $targetUsername
     */
    public function storeMessage(string $text, string $senderUsername, string $targetUsername){
        $message = new Message;
        $message->setReceiver($targetUsername);
        $message->setSender($senderUsername);
        $message->setText($text);
    }
}
