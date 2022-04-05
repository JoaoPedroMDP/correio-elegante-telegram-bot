<?php
declare(strict_types=1);


namespace App\Domains\Commands;


use App\Domains\Commands\Services\CommandServices;
use App\Domains\Core\Interfaces\CommandInterface;
use App\Domains\Core\RootClasses\ServicesAndRepositories;
use App\Domains\Update\Update;
use Exception;

/**
 * Class CommandsCommand
 * @package App\Domains\Commands
 */
class CommandsCommand implements CommandInterface
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
     * CommandsCommand constructor.
     * @param int $senderTid
     * @param ServicesAndRepositories $servicesAndRepositories
     */
    public function __construct(int $senderTid, ServicesAndRepositories $servicesAndRepositories)
    {
        $this->senderTid = $senderTid;
        $this->servicesAndRepositories = $servicesAndRepositories;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $commands = CommandServices::COMMANDS;
        $message = $this->transformCommandListInString($commands);
        $this->servicesAndRepositories->messageServices()->sendMessage(
            config('services.Telegram.botChatId'),
            $this->senderTid,
            $message
        );
    }

    /**
     * @param Update $update
     * @return CommandsCommand
     */
    public static function fromUpdate(Update $update): CommandsCommand
    {
        return new self(
            $update->senderTid,
            new ServicesAndRepositories()
        );
    }

    /**
     * @param array $commands
     * @return string
     */
    private function transformCommandListInString(array $commands): string
    {
        $string = '';
        foreach($commands as $command => $description)
        {
            $string .= "$command => $description\n\n";
        }

        return $string;
    }
}
