<?php
declare(strict_types=1);


namespace App\Domains\Core\Services;


use App\Domains\Commands\Exceptions\UserAlreadyRegistered;
use App\Domains\Commands\Exceptions\UserDidNotRepliedToMessageManually;
use App\Domains\Commands\ReplyCommand;
use App\Domains\Commands\SendCommand;
use App\Domains\Core\RootClasses\ServicesAndRepositories;
use App\Domains\Update\Update;
use Webmozart\Assert\Assert;

/**
 * Class ValidatorServices
 * @package App\Domains\Core\Services
 */
class ValidatorServices extends ServicesAndRepositories
{
    private const USERNAME_REGEX = "/^[a-zA-Z\d_]{5,32}$/";
    private const COMMAND_REGEX = "/^\/[a-zA-Z]{4,32}$/";
    private const MESSAGE_REGEX = '/^[^"$]{1,590}$/';

    /**
     * @param Update $update
     */
    public function validateSendData(Update $update)
    {
        $words = explode(" ", $update->rawText);

        Assert::keyExists($words, 1, "Você precisa definir para quem vai mandar a mensagem");
        Assert::keyExists($words, 2, "Você precisa definir uma mensagem");

        Assert::regex($words[0],self::COMMAND_REGEX,"Padrão de comando inválido");
        Assert::regex($words[1],self::USERNAME_REGEX,"Padrão de username inválido");

        $justMessage = array_slice($words, SendCommand::SEND_COMMAND_MESSAGE_OFFSET);
        $justMessage = implode(' ', $justMessage);
        Assert::regex($justMessage,self::MESSAGE_REGEX,"Padrão de mensagem inválido");
    }

    /**
     * @param Update $update
     * @throws UserAlreadyRegistered
     */
    public function validateStartData(Update $update)
    {
        $user = $this->userRepository()->getUserByTid($update->senderTid);
        if(isset($user)){
            throw new UserAlreadyRegistered();
        }
    }

    /**
     * @param Update $update
     * @throws UserDidNotRepliedToMessageManually
     */
    public function validateReplyData(Update $update)
    {
        if(!isset($update->rawUpdateData['message']['reply_to_message']))
        {
            throw new UserDidNotRepliedToMessageManually();
        }

        $words = explode(' ', $update->rawText);
        $justMessage = array_slice($words, ReplyCommand::REPLY_COMMAND_MESSAGE_OFFSET);
        $justMessage = implode(' ', $justMessage);
        Assert::regex($justMessage,self::MESSAGE_REGEX,"Padrão de mensagem inválido");
    }
}
