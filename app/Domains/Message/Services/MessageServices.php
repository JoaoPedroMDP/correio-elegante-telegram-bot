<?php
declare(strict_types=1);


namespace App\Domains\Message\Services;

use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Message\Persistence\MessageRepository;

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
     * MessageServices constructor.
     */
    public function __construct(){
        $this->messageRepository = new MessageRepository();
    }

    /**
     * @param CommandInterface $update
     */
    public function registerNewMessage(CommandInterface $update): void
    {
        $message = $update->getMessage();
        $sender = $update->getSender();
        $target = $update->getTarget();

        $this->messageRepository->storeMessage($message, $sender->getUsername(), $target->getUsername());
    }
}
