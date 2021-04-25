<?php
declare(strict_types=1);


namespace App\Domains\Message\Handlers;


use App\Domains\Core\Services\UpdateServices;
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
     * @param TelegramServices $telegramServices
     * @param UpdateServices $updateServices
     */
    public function __construct(TelegramServices $telegramServices, UpdateServices $updateServices)
    {
        $this->telegramServices = $telegramServices;
        $this->updateServices = $updateServices;
    }

    public function handle(){
        $messageCount = 0;

        try{

            $updates = $this->telegramServices->getUpdates();
            $messageCount = $updates->count();
            $this->updateServices->handleUpdates($updates);

        }catch(NoMessages $noMessages){
            TeleLogger::log($noMessages->getMessage(),'info');
        }catch(Exception $exception){
            TeleLogger::log($exception->getMessage(),'error');
            TeleLogger::log("\n" . $exception->getTraceAsString(),'error');
        }catch(Throwable $throwable){
            TeleLogger::log($throwable->getMessage(),'error');
            TeleLogger::log("\n" . $throwable->getTraceAsString(),'error');
        }

        $now = Carbon::now()->format("d/m/Y i:s");
        TeleLogger::log("$messageCount mensagens foram processadas em $now",'info');
    }
}
