<?php
declare(strict_types=1);


namespace App\Domains\Core\Services;


use App\Domains\Commands\SendCommand;
use App\Domains\Core\Utils\TeleLogger;
use App\Domains\Message\Services\MessageServices;
use App\Domains\User\Services\UserServices;
use Exception;
use Illuminate\Support\Collection as SupportCollection;
use App\Domains\User\Exceptions\UserNotFound;

/**
 * Class UpdateServices
 * @package App\Domains\Core\Services
 */
class UpdateServices
{

    /**
     * @var MessageServices
     */
    private $messageServices;

    /**
     * @var UserServices
     */
    private $userServices;

    /**
     * UpdateServices constructor.
     */
    public function __construct()
    {
        $this->messageServices = new MessageServices();
        $this->userServices = new UserServices();
    }

    /**
     * @param SupportCollection $updates
     * @throws UserNotFound
     */
    public function handleUpdates(SupportCollection $updates)
    {
        $updates->each(function ($value, $key){
            $this->singleUpdate($value);
        });
    }

    /**
     * @param array $update
     * @throws UserNotFound
     */
    private function singleUpdate(array $update)
    {
        if($this->isCommand($update)){
            $command = $this->extractCommandFromMessage($update);
            try{
                $command->execute();
            }catch(Exception $exception){
                TeleLogger::log($exception->getMessage(), 'error');
            }
            $this->messageServices->registerNewMessage($command);
        }
        dd($update);
    }

    /**
     * @param array $update
     * @return bool
     */
    private function isCommand(array $update): bool
    {
        return isset($update['message']['entities']);
    }

    /**
     * @param array $update
     * @return SendCommand
     * @throws UserNotFound
     */
    private function extractCommandFromMessage(array $update): SendCommand
    {
//        dd($update);
        $commandString = $update['message']['entities'];

        $sender = $this->userServices->getUserByChatId($update['message']['from']['id']);
        $target = $this->userServices->getUserByChatId($update['message']['from']['id']);
        $text = $this->extractTextFromMessage($update['message']);
        dd($text);
        switch($commandString){
            case '/send':
                return new SendCommand($sender,$target,'');
                break;
            case '/reply':
                break;
            case '/users':
                break;
        }
    }

    /**
     * @param array $message
     * @return false|string
     */
    private function extractTextFromMessage(array $message)
    {
        $commandLength = $message['entities'][0]['length'];

        return substr($message['text'],$commandLength);
    }
}
