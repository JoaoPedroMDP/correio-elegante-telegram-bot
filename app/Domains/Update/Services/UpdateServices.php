<?php
declare(strict_types=1);


namespace App\Domains\Update\Services;


use App\Domains\Commands\Exceptions\UserAlreadyRegistered;
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
            $update = Update::fromArray($value);
            $this->singleUpdate($update);
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
    public function singleUpdate(Update $update)
    {
        try {
            if($update->isCommand){
                $this->handleIncomingCommand($update);
            }else{
                throw new UserTalkingToBot();
            }
        } catch(Exception $e) {
            $this->messageServices()->botSend($e->getMessage(),$update->senderTid);
            throw $e;
        }
    }

    /**
     * @throws TargetNotFound
     * @throws UserAlreadyRegistered
     * @throws SenderNotFound
     * @throws CommandNotFound
     * @throws UserNotRegistered
     */
    private function handleIncomingCommand(Update $update)
    {
        $command = $this->validateAndInstantiateCommand($update);
        $command->execute();
        $command->persistMessageInDatabase();
    }

    /**
     * @param Update $update
     * @return CommandInterface
     * @throws CommandNotFound
     * @throws SenderNotFound
     * @throws TargetNotFound
     * @throws UserNotRegistered
     * @throws UserAlreadyRegistered
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
                $this->validatorServices()->validateStartData($update);
                return $this->commandServices()->instantiateStartCommand($update);
                break;
            default:
                throw new CommandNotFound($update->command);
                break;
        }
    }
}
