<?php
declare(strict_types=1);


namespace App\Domains\User\Persistence;


use App\User;

/**
 * Class UserRepository
 * @package App\Domains\User
 */
class UserRepository
{

    /**
     * @param int $id
     * @return User|null
     */
    public function getUserByChatId(int $id): ?User
    {
        $user = new User();
        return $user->find($id);
    }
}
