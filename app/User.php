<?php
declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * @package App
 * @mixin Builder
 */
class User extends Authenticatable
{
    use Notifiable;

//    /**
//     * @var string
//     */
//    private $name;
//
//    /**
//     * @var string
//     */
//    private $fakeIdentifier;
//
//    /**
//     * @var string
//     */
//    private $username;
//
//    /**
//     * @var int
//     */
//    private $chat_id;
//
//    /**
//     * @var boolean
//     */
//    private $is_bot;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'fakeIdentifier', 'username',
        'chat_id', 'is_bot'
    ];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getFakeIdentifier(): string
    {
        return $this->fakeIdentifier;
    }

    /**
     * @param string $fakeIdentifier
     */
    public function setFakeIdentifier(string $fakeIdentifier): void
    {
        $this->fakeIdentifier = $fakeIdentifier;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return $this->chat_id;
    }

    /**
     * @param int $chat_id
     */
    public function setChatId(int $chat_id): void
    {
        $this->chat_id = $chat_id;
    }

    /**
     * @return bool
     */
    public function isIsBot(): bool
    {
        return $this->is_bot;
    }

    /**
     * @param bool $is_bot
     */
    public function setIsBot(bool $is_bot): void
    {
        $this->is_bot = $is_bot;
    }

}
