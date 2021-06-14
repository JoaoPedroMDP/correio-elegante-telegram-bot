<?php
declare(strict_types=1);


namespace App\Domains\Update\Services;


use App\Domains\Commands\SendCommand;
use App\Domains\Commands\Services\CommandServices;
use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Core\Services\CoreServices;
use App\Domains\Core\Services\ValidatorServices;
use App\Domains\Core\Utils\TeleLogger;
use App\Domains\Message\Exceptions\UserTalkingToBot;
use App\Domains\Message\Services\MessageServices;
use App\Domains\Telegram\Update;
use App\Domains\Update\Exceptions\CommandNotFound;
use App\Domains\User\Exceptions\Message\SenderNotFound;
use App\Domains\User\Exceptions\Message\TargetNotFound;
use App\Domains\User\Exceptions\MessageException;
use App\Domains\User\Exceptions\User\UserNotRegistered;
use App\Domains\User\Exceptions\User\UserNotFound;
use App\Domains\User\Services\UserServices;
use Exception;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Class UpdateServices
 * @package App\Domains\Update\Services
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
     * @var CommandServices
     */
    private $commandServices;

    /**
     * @var CoreServices
     */
    private $coreServices;

    /**
     * @var ValidatorServices
     */
    private $validatorServices;

    /**
     * UpdateServices constructor.
     */
    public function __construct()
    {
        $this->messageServices = new MessageServices();
        $this->userServices = new UserServices();
        $this->commandServices = new CommandServices();
        $this->coreServices = new CoreServices();
        $this->validatorServices = new ValidatorServices();
    }

    /**
     * @param SupportCollection $updates
     * @throws UserNotFound|Exception
     */
    public function handleUpdates(SupportCollection $updates)
    {
        $updates->each(function ($value, $key){
            $update = new Update($value);

            try {
                $this->singleUpdate($update);
            } catch(MessageException $e) {
                $this->coreServices->handleMessageException($e);
            }
        });
    }

    /**
     * @param Update $update
     * @throws UserTalkingToBot
     */
    private function singleUpdate(Update $update)
    {
        if($update->isCommand){

            try{
                $command = $this->validateAndInstantiateCommand($update);
                $command->execute();
                $command->persistMessageInDatabase();
            }catch(Exception $exception){
                TeleLogger::log($exception->getMessage(), 'error');
            }

        }else{
            throw new UserTalkingToBot($update->getSenderTid());
        }
    }

    /**
     * @param Update $update
     * @return CommandInterface
     * @throws CommandNotFound
     * @throws SenderNotFound
     * @throws TargetNotFound
     */
    private function validateAndInstantiateCommand(Update $update): CommandInterface
    {
        switch($update->command){
            case 'send':
                $this->validatorServices->validateAndReturnSendData($update);
                return $this->commandServices->instantiateSendCommand($update);
                break;
            case 'reply':
                break;
            case 'users':
                break;
            case 'start':
                return $this->commandServices->instantiateStartCommand($update);
                break;
            default:
                throw new CommandNotFound($update->command);
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
