<?php
declare(strict_types=1);


namespace App\Domains\Core\Services;


use App\Domains\Commands\SendCommand;
use App\Domains\Core\RootClasses\ServicesAndRepositories;
use App\Domains\Update\Update;
use App\Domains\User\Exceptions\User\UserNotFound;
use App\Domains\User\Exceptions\User\UserNotRegistered;
use Webmozart\Assert\Assert;

/**
 * Class ValidatorServices
 * @package App\Domains\Core\Services
 */
class ValidatorServices extends ServicesAndRepositories
{
    private const USERNAME_REGEX = "/^[a-zA-Z\d_]{5,32}$/";
    private const COMMAND_REGEX = "/^\/[a-zA-Z]{4,32}$/";
    private const MESSAGE_REGEX = '/^[^"$]{1,500}$/';

    /**
     * @param Update $update
     * @throws UserNotRegistered
     */
    public function validateSendData(Update $update)
    {
        $this->checkIfSenderIsRegistered($update);
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
