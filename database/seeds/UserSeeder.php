<?php
declare(strict_types=1);

use App\User;
use Illuminate\Database\Seeder;

/**
 * Class UserSeeder
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->create(
            [
                'name' => env("BOT_NAME"),
                'fakeIdentifier' => env("BOT_IDENTIFIER"),
                'username' => env("BOT_USERNAME"),
                'chat_id' => env("BOT_CHATID"),
                'is_bot' => true
            ]
        );
    }
}
