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
                'name' => config('services.Telegram.botName'),
                'fakeIdentifier' => config('services.Telegram.botFakeIdentifier'),
                'username' => config('services.Telegram.botUsername'),
                'chat_id' => config('services.Telegram.botChatId'),
                'is_bot' => true
            ]
        );
    }
}
