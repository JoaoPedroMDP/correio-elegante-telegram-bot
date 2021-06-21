<?php
declare(strict_types=1);


namespace App\Domains\Update\Services;


use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Core\RootClasses\ServicesAndRepositories;
use App\Domains\Core\Utils\TeleLogger;
use App\Domains\Message\Exceptions\UserTalkingToBot;
use App\Domains\Update\Exceptions\CommandNotFound;
use App\Domains\Update\Update;
use App\Domains\User\Exceptions\Message\SenderNotFound;
use App\Domains\User\Exceptions\Message\TargetNotFound;
use App\Domains\User\Exceptions\User\UserNotRegistered;
use Exception;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Class UpdateServices
 * @package App\Domains\Update\Services
 */
class UpdateServices extends ServicesAndRepositories
{
    /**
     * @param SupportCollection $updates
     * @throws Exception
     */
    public function handleUpdates(SupportCollection $updates)
    {
        $updates->each(function ($value, $key){
            $update = new Update($value);

            try {
                $this->singleUpdate($update);
            } catch(Exception $e) {
                $this->messageServices()->botSend($e->getMessage(),$update->senderTid);
                throw $e;
            }
        });
    }

    /**
     * @param Update $update
     * @throws CommandNotFound
     * @throws SenderNotFound
     * @throws TargetNotFound
     * @throws UserTalkingToBot
     * @throws UserNotRegistered
     * @throws Exception
     */
    private function singleUpdate(Update $update)
    {
        if($update->isCommand){

            try{
                $command = $this->validateAndInstantiateCommand($update);
                $command->execute();
                $command->persistMessageInDatabase();
            }catch(Exception $e){
                $this->messageServices()->botSend($e->getMessage(), $update->senderTid);
                TeleLogger::log($e->getMessage(), 'error');
                throw $e;
            }

        }else{
            throw new UserTalkingToBot();
        }
    }

    /**
     * @param Update $update
     * @return CommandInterface
     * @throws CommandNotFound
     * @throws SenderNotFound
     * @throws TargetNotFound
     * @throws UserNotRegistered
     */
    private function validateAndInstantiateCommand(Update $update): CommandInterface
    {
        switch($update->command){
            case 'send':
                $this->validatorServices()->validateSendData($update);
                return $this->commandServices()->instantiateSendCommand($update);
                break;
            case 'reply':
                break;
            case 'users':
                break;
            case 'start':
                return $this->commandServices()->instantiateStartCommand($update);
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
