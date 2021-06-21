<?php
declare(strict_types=1);


namespace App\Domains\Core\RootClasses;


/**
 * Class Command
 * @package App\Domains\Core\RootClasses
 */
class Command
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
     * @var boolean
     */
    protected $isBot = false;

    /**
     * @var int
     */
    protected $messageTid;

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
