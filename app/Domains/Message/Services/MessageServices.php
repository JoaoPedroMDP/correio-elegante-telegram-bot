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
     * @param array $data
     */
    public function registerNewMessage(array $data): void
    {
        $this->messageRepository->storeMessage(
            $data['message'],
            $data['senderTid'],
            $data['targetTid']
        );
    }

    /**
     * @param string $senderTid
     * @param string $targetTid
     * @param string $message
     * @throws Exception
     */
    public function sendMessage(string $senderTid, string $targetTid, string $message)
    {
        $this->telegramServices->sendMessage($message, intval($targetTid));

        $params = [
            "message" => $message,
            "senderTid" => $senderTid,
            "targetTid" => $targetTid
        ];
        $this->registerNewMessage($params);
    }
}
