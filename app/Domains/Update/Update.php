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
     * Update constructor.
     */
    public function __construct(array $updateData)
    {
        $this->rawText = $updateData['message']['text'];
        $this->isBot = $updateData['message']['from']['is_bot'];
        $this->senderName = $updateData['message']['from']['first_name'] ?? null;
        $this->senderTid = $updateData['message']['from']['id'];
        $this->senderUsername = $updateData['message']['from']['username'];
        $this->messageTid = $updateData['message']['message_id'];

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
}
