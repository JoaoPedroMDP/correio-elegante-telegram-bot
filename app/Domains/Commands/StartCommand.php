<?php
declare(strict_types=1);


namespace App\Domains\Commands;


use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Core\RootClasses\Command;
use App\Domains\Core\Services\CoreServices;
use App\Domains\Core\Utils\Utils\DefaultMessages;
use App\Domains\Message\DBChangers\MessageDBChanger;
use App\Domains\Message\Services\MessageServices;
use App\Domains\User\Exceptions\User\BotUserMissing;
use App\Domains\User\Exceptions\User\UserCouldNotBeCreated;
use App\Domains\User\Services\UserServices;
use Exception;

/**
 * Class StartCommand
 * @package App\Domains\Commands
 */
class StartCommand extends Command implements CommandInterface
{
    /**
     * @var UserServices
     */
    private $userServices;

    /**
     * @var CoreServices
     */
    private $coreServices;

    /**
     * @var MessageServices
     */
    private $messageServices;

    /**
     * StartCommand constructor.
     * @param string $senderName
     * @param string $senderUsername
     * @param int $senderTid
     * @param bool $isBot
     * @param int $messageTid
     */
    public function __construct(string $senderName, string $senderUsername, int $senderTid, bool $isBot = false, int $messageTid)
    {
        $this->senderName = $senderName;
        $this->senderUsername = $senderUsername;
        $this->senderTid = $senderTid;
        $this->isBot = $isBot;
        $this->messageTid = $messageTid;
        $this->userServices = new UserServices();
        $this->coreServices = new CoreServices();
        $this->messageServices = new MessageServices();
    }

    /**
     * @throws UserCouldNotBeCreated
     * @throws Exception
     */
    public function execute()
    {
        $this->setFakeIdentifier($this->coreServices->generateFakIdentifier());

        $params = [
            "name" => $this->senderName,
            "username"=> $this->senderUsername,
            "fakeIdentifier"=> $this->fakeIdentifier,
            "chat_id"=> $this->senderTid,
            "is_bot"=> $this->isBot
        ];

        $user = $this->userServices->storeUser($params);
        $this->messageServices->botSend(DefaultMessages::greetNewUser($user->getFakeIdentifier()), $user->getChatId());
    }

    /**
     * @throws BotUserMissing
     */
    public function persistMessageInDatabase()
    {
        $params = [
            "message" => "Usuário se registrando no bot",
            "senderTid" => $this->senderTid,
            "targetTid" => $this->userServices->returnBotUser()->getChatId()
        ];
        $this->messageServices->registerNewMessage(MessageDBChanger::fromArray($params));
    }

}
