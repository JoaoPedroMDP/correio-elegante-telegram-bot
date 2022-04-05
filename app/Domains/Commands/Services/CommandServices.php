<?php
declare(strict_types=1);


namespace App\Domains\Commands\Services;


use App\Domains\Commands\SendCommand;
use App\Domains\Commands\StartCommand;
use App\Domains\Core\RootClasses\ServicesAndRepositories;
use App\Domains\Update\Update;
use App\Domains\User\Exceptions\Message\{SenderNotFound, TargetNotFound};
use App\User;

/**
 * Class CommandServices
 * @package App\Domains\Commands\Services
 */
class CommandServices extends ServicesAndRepositories
{

    public const COMMANDS = [
        'send' => 'Usado para enviar mensagens. O uso é "/send Username_da_pessoa Mensagem para ela"',
        'start' => 'Usado para se registrar no bot',
        'reply' => 'Usado para responder a alguém. Para usar, responda a mensagem manualmente e na resposta escreva /reply',
        'users' => 'Usado para listar todos os usuários registrados no bot',
        'commands' => 'Lista todos os comandos do bot'
    ];

    /**
     * @param Update $update
     * @return SendCommand
     * @throws SenderNotFound
     * @throws TargetNotFound
     */
    public function instantiateSendCommand(Update $update): SendCommand
    {
        $sender = $this->getSenderFromTid(
            $update->senderTid
        );
        $targetUsername = $this->extractUsernameFromRawText($update->rawText);
        $target = $this->getTargetFromUsername($targetUsername);

        return new SendCommand($sender, $target, $update->rawText);
    }

    /**
     * @param Update $update
     * @return StartCommand
     */
    public function instantiateStartCommand(Update $update): StartCommand
    {
        return StartCommand::fromUpdate($update);
    }

    /**
     * @param int $senderTid
     * @return User
     * @throws SenderNotFound
     */
    public function getSenderFromTid(int $senderTid): User
    {
        $user = $this->userRepository()->getUserByTid($senderTid);
        if(is_null($user)){
            throw new SenderNotFound();
        }

        return $user;
    }

    /**
     * @param string $username
     * @return User
     * @throws TargetNotFound
     */
    public function getTargetFromUsername(string $username): User
    {
        $user = $this->userServices()->getUserByUsername($username);
        if(is_null($user)){
            throw new TargetNotFound();
        }

        return $user;
    }

    /**
     * @param string $rawText
     * @return string
     */
    public function extractUsernameFromRawText(string $rawText): string
    {
        $words = explode(' ', $rawText);
        return $words[1];
    }
}
