<?php
declare(strict_types=1);


namespace App\Domains\Update;

/**
 * Class Update
 * @package App\Domains\Update
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
     * @var int
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
     * @var bool
     */
    public $isBot;

    /**
     * @var int
     */
    public $messageTid;

    /**
     * @var array
     */
    public $rawUpdateData;

    /**
     * Update constructor.
     * @param string $rawText
     * @param bool $isCommand
     * @param string|null $command
     * @param int $senderTid
     * @param string $senderUsername
     * @param string|null $senderName
     * @param bool $isBot
     * @param int $messageTid
     * @param array $rawUpdateData
     */
    public function __construct(string $rawText, bool $isCommand, ?string $command, int $senderTid, string $senderUsername, ?string $senderName, bool $isBot, int $messageTid, array $rawUpdateData)
    {
        $this->rawText = $rawText;
        $this->isCommand = $isCommand;
        $this->command = $command;
        $this->senderTid = $senderTid;
        $this->senderUsername = $senderUsername;
        $this->senderName = $senderName;
        $this->isBot = $isBot;
        $this->messageTid = $messageTid;
        $this->rawUpdateData = $rawUpdateData;
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

    public static function fromArray(array $data): Update
    {
        $rawText = $data['message']['text'];
        $isBot = $data['message']['from']['is_bot'];
        $senderName = $data['message']['from']['first_name'] ?? null;
        $senderTid = $data['message']['from']['id'];
        $senderUsername = $data['message']['from']['username'];
        $messageTid = $data['message']['message_id'];

        $isCommand = false;
        $command = '';
        if(isset($data['message']['entities']) &&
            $data['message']['entities'][0]['type'] == 'bot_command')
        {
            $isCommand = true;
            $command = self::extractCommand(
                $data['message']['entities'][0]['offset'],
                $data['message']['entities'][0]['length'],
                $data['message']['text']
            );
        }

        return new self (
            $rawText,
            $isCommand,
            $command,
            $senderTid,
            $senderUsername,
            $senderName,
            $isBot,
            $messageTid,
            $data
        );
    }
}
