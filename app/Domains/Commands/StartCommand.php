<?php
declare(strict_types=1);


namespace App\Domains\Commands;


use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Core\Services\CoreServices;
use App\Domains\Message\Services\MessageServices;
use App\Domains\User\Exceptions\User\BotUserMissing;
use App\Domains\User\Exceptions\User\UserCouldNotBeCreated;
use App\Domains\User\Services\UserServices;
use Exception;

/**
 * Class StartCommand
 * @package App\Domains\Commands
 */
class StartCommand implements CommandInterface
{
    /**
     * @var string
     */
    private $senderName;

    /**
     * @var string
     */
    private $senderUsername;

    /**
     * @var string
     */
    private $fakeIdentifier;

    /**
     * @var integer
     */
    private $senderTid;

    /**
     * @var boolean
     */
    private $isBot = false;

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
     */
    public function __construct(string $senderName, string $senderUsername, int $senderTid, bool $isBot = false)
    {
        $this->senderName = $senderName;
        $this->senderUsername = $senderUsername;
        $this->senderTid = $senderTid;
        $this->isBot = $isBot;
        $this->userServices = new UserServices();
        $this->coreServices = new CoreServices();
        $this->messageServices = new MessageServices();
    }

    public function execute()
    {
        try{
            $this->setFakeIdentifier($this->coreServices->generateFakIdentifier());

            $params = [
                "name" => $this->senderName,
                "username"=> $this->senderUsername,
                "fakeIdentifier"=> $this->fakeIdentifier,
                "chat_id"=> $this->senderTid,
                "is_bot"=> $this->isBot
            ];

            $user = $this->userServices->storeUser($params);
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
     * @throws BotUserMissing
     */
    public function persistMessageInDatabase()
    {
        $params = [
            "message" => "Usuário se registrando no bot",
            "senderTid" => $this->senderTid,
            "targetTid" => $this->userServices->returnBotUser()->getChatId()
        ];
        $this->messageServices->registerNewMessage(
            $text = "Usuário se registrando no bot",
            $sender = $this->senderUsername,
            $target = $this->userServices->returnBotUser()->getUsername()
        );
    }

    // GETTERS && SETTERS GETTERS && SETTERS GETTERS && SETTERS GETTERS && SETTERS

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     */
    public function setSenderName(string $senderName): void
    {
        $this->senderName = $senderName;
    }

    /**
     * @return string
     */
    public function getSenderUsername(): string
    {
        return $this->senderUsername;
    }

    /**
     * @param string $senderUsername
     */
    public function setSenderUsername(string $senderUsername): void
    {
        $this->senderUsername = $senderUsername;
    }

    /**
     * @return string
     */
    public function getFakeIdentifier(): string
    {
        return $this->fakeIdentifier;
    }

    /**
     * @param string $fakeIdentifier
     */
    public function setFakeIdentifier(string $fakeIdentifier): void
    {
        $this->fakeIdentifier = $fakeIdentifier;
    }

    /**
     * @return int
     */
    public function getSenderTid(): int
    {
        return $this->senderTid;
    }

    /**
     * @param int $senderTid
     */
    public function setSenderTid(int $senderTid): void
    {
        $this->senderTid = $senderTid;
    }

    /**
     * @return bool
     */
    public function isBot(): bool
    {
        return $this->isBot;
    }

    /**
     * @param bool $isBot
     */
    public function setIsBot(bool $isBot): void
    {
        $this->isBot = $isBot;
    }

}
