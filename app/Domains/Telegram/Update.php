<?php
declare(strict_types=1);


namespace App\Domains\Telegram;


use App\Domains\Core\Interfaces\UpdateInterface;

/**
 * Class Update
 * @package App\Domains\Telegram
 */
class Update implements UpdateInterface
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $senderId;

    /**
     * @var string
     */
    private $targetId;

    /**
     * Update constructor.
     * @param string $message
     * @param string $senderId
     * @param string $targetId
     */
    public function __construct(string $message, string $senderId, string $targetId)
    {
        $this->message = $message;
        $this->senderId = $senderId;
        $this->targetId = $targetId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getSenderId(): string
    {
        return $this->senderId;
    }

    /**
     * @return string
     */
    public function getTargetId(): string
    {
        return $this->targetId;
    }
}
