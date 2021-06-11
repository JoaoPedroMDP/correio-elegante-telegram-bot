<?php
declare(strict_types=1);


namespace App\Domains\User\Persistence;


use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRepository
 * @package App\Domains\User
 */
class UserRepository
{

    /**
     * Retrieves the user based on its Telegram User Id
     * @param int $id
     * @return User|null
     */
    public function getUserByTid(int $id): ?User
    {
        $user = new User();
        return $user->where('chat_id',$id)->first();
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function getUserByUsername(string $username): ?User
    {
        $user = new User();
        return $user->where('username', $username)->first();
    }
}
