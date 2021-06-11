<?php
declare(strict_types=1);


namespace App\Domains\Message\Handlers;


use App\Domains\Update\Services\UpdateServices;
use App\Domains\Telegram\Exception\NoMessages;
use App\Domains\Telegram\Services\TelegramServices;
use Exception;
use App\Domains\Core\Utils\TeleLogger;
use Throwable;
use Carbon\Carbon;

/**
 * Class GetUpdatesAction
 * @package App\Domains\Message\Handlers
 */
class GetUpdatesAction
{
    /**
     * @var TelegramServices
     */
    private $telegramServices;

    /**
     * @var UpdateServices
     */
    private $updateServices;

    /**
     * GetUpdatesAction constructor.
     */
    public function __construct()
    {
        $this->telegramServices = new TelegramServices();
        $this->updateServices = new UpdateServices();
    }

    public function handle(){
        $messageCount = 0;

        try{

            $updates = $this->telegramServices->getUpdates();
            $messageCount = $updates->count();
            $this->updateServices->handleUpdates($updates);

            $now = Carbon::now()->format("d/m/Y i:s");
            TeleLogger::log("$messageCount mensagens foram processadas em $now",'info');
        }catch(NoMessages $noMessages){
            TeleLogger::log($noMessages->getMessage(),'info');
        }catch(Exception $exception){
            dd($exception);
            TeleLogger::log($exception->getMessage(),'error');
            TeleLogger::log("\n" . $exception->getTraceAsString(),'error');
        }catch(Throwable $throwable){
            dd($throwable);
            TeleLogger::log($throwable->getMessage(),'error');
            TeleLogger::log("\n" . $throwable->getTraceAsString(),'error');
        }
    }
}
