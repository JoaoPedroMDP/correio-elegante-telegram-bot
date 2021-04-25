<?php
declare(strict_types=1);


namespace App\Domains\Commands;


use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Telegram\Handlers\TelegramServices;
use App\User;
use Exception;

/**
 * Class SendCommand
 * @package App\Domains\Commands
 */
class SendCommand implements CommandInterface
{
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
     * SendCommand constructor.
     * @param User $sender
     * @param User $target
     * @param string $message
     */
    public function __construct(User $sender, User $target, string $message)
    {
        $this->sender = $sender;
        $this->target = $target;
        $this->message = $message;
        $this->telegramServices = new TelegramServices();
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
     * @return User
     */
    public function getSender(): User
    {
        return $this->sender;
    }

    /**
     * @param User $sender
     */
    public function setSender(User $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return User
     */
    public function getTarget(): User
    {
        return $this->target;
    }

    /**
     * @param User $target
     */
    public function setTarget(User $target): void
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
