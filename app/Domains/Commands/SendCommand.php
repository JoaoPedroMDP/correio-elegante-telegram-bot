<?php
declare(strict_types=1);


namespace App\Domains\Commands;


use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Message\Services\MessageServices;
use App\Domains\Telegram\Services\TelegramServices;
use App\User;
use Exception;

/**
 * Class SendCommand
 * @package App\Domains\Commands
 */
class SendCommand implements CommandInterface
{
    private const SEND_COMMAND_MESSAGE_OFFSET = 2;
    /**
     * @var User
     */
    private $sender;

    /**
     * @var User
     */
    private $target;

    /**
     * @var string
     */
    private $message;

    /**
     * @var TelegramServices
     */
    private $telegramServices;

    /**
     * @var MessageServices
     */
    private $messageServices;

    /**
     * SendCommand constructor.
     * @param User $sender
     * @param User $target
     * @param string $rawText
     */
    public function __construct(User $sender, User $target, string $rawText)
    {
        $this->telegramServices = new TelegramServices();
        $this->messageServices = new MessageServices();

        $this->sender = $sender;
        $this->target = $target;

        $words = explode(' ', $rawText);
        for( $i = self::SEND_COMMAND_MESSAGE_OFFSET ; $i < count($words) - 1 ; $i++){
            $this->message .= $words[$i];
        }
    }

    public function execute()
    {
        try{
            $this->telegramServices->sendMessage($this->message, $this->target->getChatId());
        }catch(Exception $exception){
            $this->handleException($exception);
        }
    }

    /**
     * @param Exception $exception
     */
    public function handleException(Exception $exception)
    {
        // TODO: Implement handleException() method.
    }

    /**
     * Persists this message in database
     */
    public function persistMessageInDatabase()
    {
        $this->messageServices->registerNewMessage($this->message, $this->sender->getUsername(), $this->target->getUsername());
    }
}
