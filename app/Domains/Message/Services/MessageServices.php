<?php
declare(strict_types=1);


namespace App\Domains\Message\Services;

use App\Domains\Message\Persistence\MessageRepository;
use App\Domains\Telegram\Services\TelegramServices;
use Exception;

/**
 * Class MessageService
 * @package App\Domains\Message
 */
class MessageServices
{

    /**
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * @var TelegramServices
     */
    private $telegramServices;

    /**
     * MessageServices constructor.
     */
    public function __construct(){
        $this->messageRepository = new MessageRepository();
        $this->telegramServices = new TelegramServices();
    }

    /**
     * @param string $text
     * @param string $sender
     * @param string $target
     */
    public function registerNewMessage(string $text, string $sender, string $target): void
    {
        $this->messageRepository->storeMessage($text, $sender, $target);
    }

    /**
     * @param string $senderChatId
     * @param string $targetChatId
     * @param string $message
     * @throws Exception
     */
    public function sendMessage(string $senderChatId, string $targetChatId, string $message)
    {
        $this->telegramServices->sendMessage($message, intval($targetChatId));

        $this->registerNewMessage($message,$senderChatId, $targetChatId);
    }
}
