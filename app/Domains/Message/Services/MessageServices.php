<?php
declare(strict_types=1);


namespace App\Domains\Message\Services;

use App\Domains\Core\RootClasses\ServicesAndRepositories;
use App\Domains\Message\DBChangers\MessageDBChanger;
use App\Domains\Message\Exceptions\MessageNotFound;
use App\Message;
use Exception;

/**
 * Class MessageService
 * @package App\Domains\Message
 */
class MessageServices extends ServicesAndRepositories
{
    // Define o caractere que irá separar a cor na mensagem
    public const COLOR_SEPARATOR_BEFORE = '<<';
    public const COLOR_SEPARATOR_AFTER = '>>';

    private const MYSQL_DATA_TOO_LONG_ERROR_CODE = 22001;
    /**
     * @param MessageDBChanger $changer
     */
    public function registerNewMessage(MessageDBChanger $changer): void
    {
        try{
            $this->messageRepository()->storeMessage($changer);
        }catch(Exception $e){
        }
    }

    /**
     * @param int $senderTid
     * @param int $targetTid
     * @param string $message
     * @throws Exception
     */
    public function sendMessage(int $senderTid, int $targetTid, string $message)
    {
        $messageTid = $this->telegramServices()->sendMessage($message, intval($targetTid));

        $params = [
            "message" => $message,
            "messageTid" => $messageTid,
            "senderTid" => $senderTid,
            "targetTid" => $targetTid
        ];

        $this->registerNewMessage(MessageDBChanger::fromArray($params));
    }

    /**
     * @param string $message
     * @param int $targetTid
     * @throws Exception
     */
    public function botSend(string $message, int $targetTid)
    {
        $this->sendMessage(
            config("services.Telegram.botChatId"),
            $targetTid,
            $message
        );
    }

    /**
     * @param int $tid
     * @return Message
     * @throws MessageNotFound
     */
    public function getMessageByTid(int $tid): Message
    {
        $message = $this->messageRepository()->getMessageByTid($tid);
        if(is_null($message))
        {
            throw new MessageNotFound();
        }

        return $message;
    }
}
