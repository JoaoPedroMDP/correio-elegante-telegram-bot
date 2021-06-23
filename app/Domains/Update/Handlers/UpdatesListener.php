<?php
declare(strict_types=1);


namespace App\Domains\Update\Handlers;


use App\Domains\Core\Utils\TeleLogger;
use App\Domains\Update\Services\UpdateServices;
use App\Domains\Update\Update;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Throwable;

/**
 * Class UpdatesListener
 * @package App\Domains\Update\Handlers
 */
class UpdatesListener
{

    /**
     * @var UpdateServices
     */
    private $updateServices;

    /**
     * GetUpdates constructor.
     */
    public function __construct()
    {
        $this->updateServices = new UpdateServices();
    }

    /**
     * @param Request $request
     */
    public function handle(Request $request){
        try{
            $this->updateServices->singleUpdate(Update::fromArray($request->toArray()));
            $now = Carbon::now()->format("d/m/Y H:i:s");
            TeleLogger::log("Mensagem recebida em $now",'info');
        }catch(Exception $exception){
            TeleLogger::log($exception->getMessage(),'error');
            TeleLogger::log("\n" . $exception->getTraceAsString(),'error');
        }catch(Throwable $throwable){
            TeleLogger::log($throwable->getMessage(),'error');
            TeleLogger::log("\n" . $throwable->getTraceAsString(),'error');
        }
    }
}
