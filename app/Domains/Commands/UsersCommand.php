<?php
declare(strict_types=1);


namespace App\Domains\Commands;


use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Core\RootClasses\ServicesAndRepositories;
use App\Domains\Update\Update;
use App\Domains\User\Exceptions\User\BotUserMissing;
use App\Domains\User\Exceptions\User\UserNotFound;
use App\Domains\User\Exceptions\User\UserNotRegistered;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Class UsersCommand
 * @package App\Domains\Commands
 */
class UsersCommand implements CommandInterface
{
    /**
     * @var int
     */
    private $senderTid;

    /**
     * @var ServicesAndRepositories
     */
    private $servicesAndRepositories;

    /**
     * UsersCommand constructor.
     * @param int $senderTid
     * @param ServicesAndRepositories $servicesAndRepositories
     */
    public function __construct(int $senderTid, ServicesAndRepositories $servicesAndRepositories)
    {
        $this->senderTid = $senderTid;
        $this->servicesAndRepositories = $servicesAndRepositories;
    }

    /**
     * @throws BotUserMissing
     * @throws Exception
     */
    public function execute()
    {
        $users = $this->servicesAndRepositories->userRepository()->getUsers();

        $nameUsername = $this->getNameUsernameList($users);
        $nameUsername = $this->removeBotUser($nameUsername);

        $message = $this->transformUserListInString($nameUsername);

        $this->servicesAndRepositories->messageServices()->sendMessage(
            config('services.Telegram.botChatId'),
            $this->senderTid,
            $message
        );
    }

    /**
     * @param Update $update
     * @return UsersCommand
     */
    public static function fromUpdate(Update $update): UsersCommand
    {
        $servicesAndRepositories = new ServicesAndRepositories();
        return new self(
            $update->senderTid,
            $servicesAndRepositories
        );
    }

    /**
     * @param EloquentCollection $users
     * @return array
     */
    private function getNameUsernameList(EloquentCollection $users): array
    {
        $nameUsernameArray = [];
        foreach($users as $user)
        {
            $nameUsernameArray[$user->getName()] = $user->getUsername();
        }

        return $nameUsernameArray;
    }

    /**
     * @param array $formattedUsers
     * @return string
     */
    private function transformUserListInString(array $formattedUsers): string
    {
        $string = '';
        foreach($formattedUsers as $name => $username)
        {
            $string .= "$name => $username\n";
        }

        return $string;
    }

    private function removeBotUser(array $nameUsernameArray): array
    {
        // Teoricamente o bot é sempre o usuário 1 pois é criado no seeder
        return array_slice($nameUsernameArray, 1);
    }
}
