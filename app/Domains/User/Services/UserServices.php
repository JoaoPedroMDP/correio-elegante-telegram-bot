<?php
declare(strict_types=1);


namespace App\Domains\User\Services;


use App\Domains\User\Exceptions\User\BotUserMissing;
use App\Domains\User\Exceptions\User\UserCouldNotBeCreated;
use App\Domains\User\Exceptions\User\UserNotFound;
use App\Domains\User\Persistence\UserRepository;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserServices
 * @package App\Domains\User\Services
 */
class UserServices
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserServices constructor.
     */
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function getUserByUsername(string $username): ?User
    {
        return $this->userRepository->getUserByUsername($username);
    }

    /**
     * @param string $color
     * @return User|null
     */
    public function getUserByColor(string $color): ?User
    {
        return $this->userRepository->getUserByColor($color);
    }

    /**
     * @param array $data
     * @return User|Model
     * @throws UserCouldNotBeCreated
     */
    public function storeUser(array $data)
    {
        $user = $this->userRepository->storeUser($data);
        if(is_null($user))
        {
            throw new UserCouldNotBeCreated($data);
        }

        return $user;
    }

    /**
     * @return User
     * @throws BotUserMissing
     */
    public function returnBotUser(): User
    {
        $bot = $this->userRepository->getBot();
        if(is_null($bot)){
            throw new BotUserMissing();
        }
        return $bot;
    }

    /**
     * @param int $userTid
     * @throws UserNotFound
     */
    public function getUserByTid(int $userTid)
    {
        $user = $this->userRepository->getUserByTid($userTid);
        if(is_null($user))
        {
            throw new UserNotFound();
        }
    }
}
