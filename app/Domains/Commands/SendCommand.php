<?php
declare(strict_types=1);


namespace App\Domains\Commands;


use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Core\RootClasses\ServicesAndRepositories;
use App\Domains\Message\Services\MessageServices;
use App\Domains\Update\Update;
use App\Domains\User\Exceptions\Message\SenderNotFound;
use App\Domains\User\Exceptions\Message\TargetNotFound;
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
     * @var ServicesAndRepositories
     */
    private $servicesAndRepositories;

    /**
     * @var int|null
     */
    private $sentMessageTid;

    /**
     * @var string
     */
    protected $message;

    /**
     * SendCommand constructor.
     * @param User $sender
     * @param User $target
     * @param string $rawText
     * @param ServicesAndRepositories $servicesAndRepositories
     */
    public function __construct(User $sender, User $target, string $rawText, ServicesAndRepositories $servicesAndRepositories)
    {
        $this->servicesAndRepositories = $servicesAndRepositories;

        $this->sender = $sender;
        $this->target = $target;

        $this->extractMessage($rawText);
        $this->setIdentifierOnMessage();
    }

    /**
     * @param string $rawText
     */
    private function extractMessage(string $rawText)
    {
        $words = explode(' ', $rawText);

        $justMessage = array_slice($words, SendCommand::SEND_COMMAND_MESSAGE_OFFSET);
        $this->message = implode(' ', $justMessage);
    }

    private function setIdentifierOnMessage()
    {
        $color = $this->sender->getFakeIdentifier();

        $prefix = "Mensagem de ";
        $color = MessageServices::COLOR_SEPARATOR_BEFORE . $color . MessageServices::COLOR_SEPARATOR_AFTER;

        $this->message = "$prefix $color\n\n$this->message";
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $this->servicesAndRepositories->messageServices()->sendMessage(
            $this->sender->getChatId(),
            $this->target->getChatId(),
            $this->message
        );
    }

    /**
     * @param Update $update
     * @return SendCommand
     * @throws SenderNotFound
     * @throws TargetNotFound
     */
    public static function fromUpdate(Update $update): SendCommand
    {
        $servicesAndRepositories = new ServicesAndRepositories();
        $sender = $servicesAndRepositories->commandServices()->getSenderFromTid(
            $update->senderTid
        );

        $targetUsername = $servicesAndRepositories->commandServices()->extractUsernameFromRawText($update->rawText);
        $target = $servicesAndRepositories->commandServices()->getTargetFromUsername($targetUsername);

        return new self(
            $sender,
            $target,
            $update->rawText,
            $servicesAndRepositories
        );
    }
}
