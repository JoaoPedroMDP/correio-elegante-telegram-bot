<?php
declare(strict_types=1);


namespace App\Domains\Commands;


use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Core\RootClasses\ServicesAndRepositories;
use App\Domains\Message\DBChangers\MessageDBChanger;
use App\Domains\Message\Exceptions\MessageNotFound;
use App\Domains\Message\Services\MessageServices;
use App\Domains\Update\Update;
use App\Domains\User\Exceptions\User\UserNotFound;
use App\Domains\User\Exceptions\User\UserNotRegistered;
use App\Message;
use App\User;
use Exception;

/**
 * Class ReplyCommand
 * @package App\Domains\Commands
 */
class ReplyCommand implements CommandInterface
{
    const REPLY_COMMAND_MESSAGE_OFFSET = 1;
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
     * @var ServicesAndRepositories
     */
    private $servicesAndRepositories;

    /**
     * ReplyCommand constructor.
     * @param User $sender
     * @param User $target
     * @param string $rawText
     */
    public function __construct(User $sender, User $target, string $rawText)
    {
        $this->sender = $sender;
        $this->target = $target;
        $this->servicesAndRepositories = new ServicesAndRepositories();

        $this->extractMessage($rawText);
        $this->setIdentifierOnMessage();
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $this->servicesAndRepositories->messageServices()->sendMessage
        (
            $this->sender->getChatId(),
            $this->target->getChatId(),
            $this->message
        );
    }

    private function setIdentifierOnMessage()
    {
        $color = $this->sender->getFakeIdentifier();

        $prefix = "Resposta de ";
        $color = MessageServices::COLOR_SEPARATOR_BEFORE . $color . MessageServices::COLOR_SEPARATOR_AFTER;

        $this->message = "$prefix $color\n\n$this->message";
    }

    /**
     * @param string $rawText
     */
    private function extractMessage(string $rawText)
    {
        $words = explode(' ', $rawText);

        $justMessage = array_slice($words, ReplyCommand::REPLY_COMMAND_MESSAGE_OFFSET);
        $this->message = implode(' ', $justMessage);
    }

    /**
     * @param Update $update
     * @return ReplyCommand
     * @throws UserNotFound
     * @throws MessageNotFound
     */
    public static function fromUpdate(Update $update): ReplyCommand
    {
        $servicesAndRepositories = new ServicesAndRepositories();
        $sender = $servicesAndRepositories->userServices()->getUserByTid($update->senderTid);

        $messageToReply = $servicesAndRepositories->messageServices()->getMessageByTid(
            $update->rawUpdateData['message']['reply_to_message']['message_id']
        );
        $target = $servicesAndRepositories->userServices()->getUserByTid($messageToReply->getSenderTid());

        return new self(
            $sender,
            $target,
            $update->rawText
        );
    }
}
