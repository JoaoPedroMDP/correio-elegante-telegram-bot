<?php
declare(strict_types=1);


namespace App\Domains\User\Services;


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
     * @param string $username
     * @return User|null
     */
    public function getUserByUsername(string $username): ?User
    {
        return $this->userRepository->getUserByUsername($username);
    }
}
