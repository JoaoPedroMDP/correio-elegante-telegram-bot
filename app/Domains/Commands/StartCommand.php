<?php
declare(strict_types=1);


namespace App\Domains\Commands;


use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Core\RootClasses\ServicesAndRepositories;
use App\Domains\Core\Utils\DefaultMessages;
use App\Domains\Message\Services\MessageServices;
use App\Domains\Update\Update;
use App\Domains\User\Exceptions\User\UserCouldNotBeCreated;
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
    protected $senderName;

    /**
     * @var string
     */
    protected $senderUsername;

    /**
     * @var string
     */
    protected $fakeIdentifier;

    /**
     * @var integer
     */
    protected $senderTid;

    /**
     * @var bool
     */
    protected $isBot;

    /**
     * @var int
     */
    protected $messageTid;

    /**
     * @var ServicesAndRepositories
     */
    private $servicesAndRepositories;

    /**
     * StartCommand constructor.
     * @param string $senderName
     * @param string $senderUsername
     * @param int $senderTid
     * @param int $messageTid
     * @param ServicesAndRepositories $servicesAndRepositories
     * @param bool $isBot
     */
    public function __construct(string $senderName, string $senderUsername, int $senderTid, int $messageTid, ServicesAndRepositories $servicesAndRepositories, bool $isBot = false)
    {
        $this->senderName = $senderName;
        $this->senderUsername = $senderUsername;
        $this->senderTid = $senderTid;
        $this->isBot = $isBot;
        $this->messageTid = $messageTid;
        $this->servicesAndRepositories = $servicesAndRepositories;
    }

    /**
     * @throws UserCouldNotBeCreated
     * @throws Exception
     */
    public function execute()
    {
        $this->setFakeIdentifier($this->servicesAndRepositories->coreServices()->generateFakIdentifier());

        $params = [
            "name" => $this->senderName,
            "username"=> $this->senderUsername,
            "fakeIdentifier"=> $this->fakeIdentifier,
            "chat_id"=> $this->senderTid,
            "is_bot"=> $this->isBot
        ];

        $user = $this->servicesAndRepositories->userServices()->storeUser($params);
        $message = $this->mountGreetingMessage($user->getFakeIdentifier());
        $this->servicesAndRepositories->messageServices()->botSend($message, $user->getChatId());
    }

    /**
     * @param string $fakeIdentifier
     * @return string
     */
    private function mountGreetingMessage(string $fakeIdentifier): string
    {
        $beforeDelimiter = MessageServices::COLOR_SEPARATOR_AFTER;
        $afterDelimiter = MessageServices::COLOR_SEPARATOR_BEFORE;
        return "Seja bem vindo ao bot! Sua cor é $beforeDelimiter$fakeIdentifier$afterDelimiter\n\nUse '/commands' para ver a lista dos comandos que existem\n\nNOTA: Todas as mensagens serão gravadas no banco de dados do operador do bot.";
    }
    /**
     * @param Update $update
     * @return StartCommand
     */
    public static function fromUpdate(Update $update): StartCommand
    {
        $servicesAndRepositories = new ServicesAndRepositories();

        return new self(
            $update->senderName,
            $update->senderUsername,
            $update->senderTid,
            $update->messageTid,
            $servicesAndRepositories,
            $update->isBot
        );
    }

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

    /**
     * @return int
     */
    public function getMessageTid(): int
    {
        return $this->messageTid;
    }

    /**
     * @param int $messageTid
     */
    public function setMessageTid(int $messageTid): void
    {
        $this->messageTid = $messageTid;
    }
}
