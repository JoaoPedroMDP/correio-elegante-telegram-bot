<?php
declare(strict_types=1);


namespace App\Domains\Core\Services;


use App\Domains\Commands\SendCommand;
use App\Domains\Telegram\Update;
use Webmozart\Assert\Assert;

/**
 * Class ValidatorServices
 * @package App\Domains\Core\Services
 */
class ValidatorServices
{
    private const USERNAME_REGEX = "/^[a-zA-Z\d_]{5,32}$/";
    private const COMMAND_REGEX = "/^\/[a-zA-Z]{4,32}$/";
    private const MESSAGE_REGEX = '/^[^"$]{1,300}$/';

    /**
     * @param Update $update
     * @return string
     */
    public function validateAndReturnSendData(Update $update): string
    {
        $message = '';
        $words = explode(" ", $update->getRawText());

        Assert::regex($words[0],self::COMMAND_REGEX,"Padrão de comando inválido");
        Assert::regex($words[1],self::USERNAME_REGEX,"Padrão de username inválido");
        Assert::regex($words[2],self::MESSAGE_REGEX,"Padrão de mensagem inválido");

        for( $i = SendCommand::SEND_COMMAND_MESSAGE_OFFSET ; $i < count($words) ; $i++){
            $message .= $words[$i];
        }

        return $message;
    }
}
