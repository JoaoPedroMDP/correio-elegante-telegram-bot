<?php
declare(strict_types=1);


namespace App\Domains\User\Persistence;


use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRepository
 * @package App\Domains\User
 */
class UserRepository
{
    const BOT_ID = 1;

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

    /**
     * @param array $data
     * @return User|Model
     */
    public function storeUser(array $data)
    {
        return (new User)->create($data);
    }

    /**
     * @return User|User[]|Collection|Model|null
     */
    public function getBot()
    {
        return (new User)->find(self::BOT_ID);
    }

    /**
     * @param string $color
     * @return User|null
     */
    public function getUserByColor(string $color): ?User
    {
        return User::where('fakeIdentifier', '=', $color)->first();
    }
}
