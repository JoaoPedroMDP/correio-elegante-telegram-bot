<?php
declare(strict_types=1);


namespace App\Domains\Telegram;

/**
 * Class Update
 * @package App\Domains\Telegram
 */
class Update
{
    /**
     * @var string
     */
    public $rawText;

    /**
     * @var boolean
     */
    public $isCommand;

    /**
     * @var string|null
     */
    public $command;

    /**
     * @var string
     */
    public $senderTid;

    /**
     * @var string
     */
    public $senderUsername;

    /**
     * @var string|null
     */
    public $senderName;

    /**
     * Update constructor.
     */
    public function __construct(array $updateData)
    {
        $this->rawText = $updateData['message']['text'];
        $this->senderName = $updateData['message']['from']['first_name'] ?? null;

        if(isset($updateData['message']['entities']) &&
            $updateData['message']['entities'][0]['type'] == 'bot_command')
        {
            $this->isCommand = true;
            $this->command = self::extractCommand(
                $updateData['message']['entities'][0]['offset'],
                $updateData['message']['entities'][0]['length'],
                $updateData['message']['text']
            );
        }

        $this->senderTid = strval($updateData['message']['from']['id']);
        $this->senderUsername = $updateData['message']['from']['username'];
    }

    /**
     * @param $offset
     * @param $length
     * @param $text
     * @return string
     */
    public static function extractCommand($offset, $length, $text): string
    {
        return substr($text,$offset + 1,$length - 1); // Para não pegar a '/'
    }

    /**
     * @return string
     */
    public function getRawText()
    {
        return $this->rawText;
    }

    /**
     * @param string $rawText
     */
    public function setRawText($rawText): void
    {
        $this->rawText = $rawText;
    }

    /**
     * @return bool
     */
    public function isCommand(): bool
    {
        return $this->isCommand;
    }

    /**
     * @param bool $isCommand
     */
    public function setIsCommand(bool $isCommand): void
    {
        $this->isCommand = $isCommand;
    }

    /**
     * @return string|null
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * @param string|null $command
     */
    public function setCommand(?string $command): void
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getSenderTid(): string
    {
        return $this->senderTid;
    }

    /**
     * @param string $senderTid
     */
    public function setSenderTid(string $senderTid): void
    {
        $this->senderTid = $senderTid;
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
     * @return string|null
     */
    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    /**
     * @param string|null $senderName
     */
    public function setSenderName(?string $senderName): void
    {
        $this->senderName = $senderName;
    }


}
