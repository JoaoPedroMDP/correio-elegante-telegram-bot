<?php
declare(strict_types=1);


namespace App\Domains\Commands\Services;


use App\Domains\Commands\SendCommand;
use App\Domains\Telegram\Update;
use App\Domains\User\Exceptions\Message\{SenderNotFound, TargetNotFound};
use App\Domains\User\Exceptions\User\UserNotFound;
use App\Domains\User\Persistence\UserRepository;
use App\Domains\User\Services\UserServices;
use App\User;

/**
 * Class CommandServices
 * @package App\Domains\Commands\Services
 */
class CommandServices
{

    /**
     * @var UserServices
     */
    private $userServices;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Instantiates UserServices for use
     */
    private function userServices(){
        $this->userServices = new UserServices();
    }

    /**
     * Instantiates UserRepository for use
     */
    private function userRepository(){
        $this->userRepository = new UserRepository();
    }

    /**
     * @param Update $update
     * @return SendCommand
     * @throws SenderNotFound
     * @throws TargetNotFound
     */
    public function instantiateSendCommand(Update $update): SendCommand
    {
        $this->userServices();
        $this->userRepository();

        $sender = $this->getSenderFromTid(
            intval($update->getSenderTid())
        );
        $targetUsername = $this->extractUsernameFromRawText($update->rawText);
        $target = $this->getTargetFromUsername($targetUsername);

        return new SendCommand($sender, $target, $update->rawText);
    }

    public function instantiateStartCommand(Update $update)
    {
        $this->userServices();
        $this->userRepository();
    }

    /**
     * @param int $senderTid
     * @return User
     * @throws SenderNotFound
     */
    private function getSenderFromTid(int $senderTid): User
    {
        $user = $this->userRepository->getUserByTid($senderTid);
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
        $user = $this->userServices->getUserByUsername($username);
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
