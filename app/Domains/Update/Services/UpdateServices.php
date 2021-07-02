<?php
declare(strict_types=1);


namespace App\Domains\Update\Services;


use App\Domains\Commands\CommandsCommand;
use App\Domains\Commands\Exceptions\UserAlreadyRegistered;
use App\Domains\Commands\Exceptions\UserDidNotRepliedToMessageManually;
use App\Domains\Commands\ReplyCommand;
use App\Domains\Commands\SendCommand;
use App\Domains\Commands\StartCommand;
use App\Domains\Commands\UsersCommand;
use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Core\RootClasses\ServicesAndRepositories;
use App\Domains\Message\Exceptions\MessageNotFound;
use App\Domains\Message\Exceptions\UserTalkingToBot;
use App\Domains\Update\Exceptions\CommandNotFound;
use App\Domains\Update\Update;
use App\Domains\User\Exceptions\Message\SenderNotFound;
use App\Domains\User\Exceptions\Message\TargetNotFound;
use App\Domains\User\Exceptions\User\UserNotFound;
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
     * @param Update $update
     * @throws CommandNotFound
     * @throws SenderNotFound
     * @throws TargetNotFound
     * @throws UserAlreadyRegistered
     * @throws UserDidNotRepliedToMessageManually
     * @throws UserNotFound
     * @throws UserNotRegistered
     * @throws MessageNotFound
     */
    private function handleIncomingCommand(Update $update)
    {
        $command = $this->validateAndInstantiateCommand($update);
        $command->execute();
    }

    /**
     * @param Update $update
     * @return CommandInterface
     * @throws CommandNotFound
     * @throws SenderNotFound
     * @throws TargetNotFound
     * @throws UserNotRegistered
     * @throws UserAlreadyRegistered
     * @throws UserNotFound
     * @throws UserDidNotRepliedToMessageManually
     * @throws MessageNotFound
     */
    private function validateAndInstantiateCommand(Update $update): CommandInterface
    {
        switch($update->command){
            case 'commands':
                return CommandsCommand::fromUpdate($update);
                break;
            case 'send':
                $this->checkIfSenderIsRegistered($update);
                $this->validatorServices()->validateSendData($update);
                return SendCommand::fromUpdate($update);
                break;
            case 'reply':
                $this->checkIfSenderIsRegistered($update);
                $this->validatorServices()->validateReplyData($update);
                return ReplyCommand::fromUpdate($update);
                break;
            case 'users':
                $this->checkIfSenderIsRegistered($update);
                return UsersCommand::fromUpdate($update);
                break;
            case 'start':
                $this->validatorServices()->validateStartData($update);
                return StartCommand::fromUpdate($update);
                break;
            default:
                throw new CommandNotFound($update->command);
                break;
        }
    }

    /**
     * @throws UserNotRegistered
     */
    private function checkIfSenderIsRegistered(Update $update)
    {
        try {
            $this->userServices()->getUserByTid($update->senderTid);
        }catch(UserNotFound $e)
        {
            throw new UserNotRegistered();
        }
    }
}
