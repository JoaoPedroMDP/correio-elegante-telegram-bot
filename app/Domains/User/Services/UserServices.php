<?php
declare(strict_types=1);


namespace App\Domains\User\Services;


use App\Domains\User\Exceptions\UserNotFound;
use App\Domains\User\Persistence\UserRepository;
use App\User;

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
     * @param int $id
     * @return User
     * @throws UserNotFound
     */
    public function getUserByChatId(int $id): User
    {
        $user = $this->userRepository->getUserByChatId($id);
        if(is_null($user)){
            throw new UserNotFound();
        }

        return $user;
    }
}
