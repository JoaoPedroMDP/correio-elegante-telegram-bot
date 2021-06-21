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
        return new StartCommand(
            $update->senderName,
            $update->senderUsername,
            $update->senderTid,
            $update->isBot,
            $update->messageTid
        );
    }

    /**
     * @param int $senderTid
     * @return User
     * @throws SenderNotFound
     */
    private function getSenderFromTid(int $senderTid): User
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
    private function getTargetFromUsername(string $username): User
    {
        $user = $this->userServices()->getUserByUsername($username);
        if(is_null($user)){
            throw new TargetNotFound($username);
        }

        return $user;
    }

    /**
     * @param string $rawText
     * @return string
     */
    private function extractUsernameFromRawText(string $rawText): string
    {
        $words = explode(' ', $rawText);
        return $words[1];
    }
}
