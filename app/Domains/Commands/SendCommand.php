<?php
declare(strict_types=1);


namespace App\Domains\Commands;


use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Message\DBChangers\MessageDBChanger;
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
    public const SEND_COMMAND_MESSAGE_OFFSET = 2;

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
     * @var int|null
     */
    private $sentMessageTid;

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

        $this->getMessage($rawText);
        $this->setIdentifierOnMessage();
    }

    /**
     * @param string $rawText
     */
    private function getMessage(string $rawText)
    {
        $words = explode(' ', $rawText);

        $justMessage = array_slice($words, SendCommand::SEND_COMMAND_MESSAGE_OFFSET);
        $this->message = implode(' ', $justMessage);
    }

    private function setIdentifierOnMessage()
    {
        $color = $this->sender->getFakeIdentifier();
        $colorSeparator = MessageServices::COLOR_SEPARATOR;

        $prefix = "Mensagem de";
        $color = $colorSeparator.$color.$colorSeparator;

        $this->message = "$prefix $color\n\n$this->message";
    }

    public function execute()
    {
        try{
            $this->sentMessageTid = $this->telegramServices->sendMessage($this->message, $this->target->getChatId());
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
        $params = [
            "message" => $this->message,
            "senderTid" => $this->sender->getChatId(),
            "targetTid" => $this->target->getChatId(),
            "messageTid" => $this->sentMessageTid
        ];
        $this->messageServices->registerNewMessage(MessageDBChanger::fromArray($params));
    }
}
