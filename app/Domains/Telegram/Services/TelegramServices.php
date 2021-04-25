<?php
declare(strict_types=1);

namespace App\Domains\Telegram\Services;

use App\Domains\Core\Utils\BodyAssemblers;
use App\Domains\Telegram\Exception\NoMessages;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\{Http, Cache};
use App\Domains\Core\Utils\Utils;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Class TelegramServices
 * @package App\Domains\Telegram\Handlers
 */
class TelegramServices {

    /**
     * @var string
     */
    private $baseURL;

    /**
     * @var string
     */
    private $botToken;

    /**
     * @var Utils
     */
    private $utils;

    /**
     * @var BodyAssemblers
     */
    private $bodyAssembler;

    /**
     * TelegramServices constructor.
     */
    public function __construct()
    {
        $this->baseURL = 'https://api.telegram.org/';
        $this->botToken = config('services.Telegram')['token'];

        $this->utils = new Utils;
        $this->bodyAssembler = new BodyAssemblers();
    }

    /**
     * @param string $message
     * @param int $targetID
     * @return Response
     * @throws Exception
     */
    public function sendMessage(string $message, int $targetID): Response
    {
        return $this->callTelegramAPI(
            $this->utils->getHttpMethod("toSendMessage"),
            $this->utils->getTelegramMethod("toSendMessage"),
            $this->bodyAssembler->assembleSendMessageBody($targetID, $message)
        );
    }

    /**
     * @param string $httpMethod
     * @param string $telegramMethod
     * @param array $body
     * @return Response
     * @throws Exception
     */
    private function callTelegramAPI(string $httpMethod, string $telegramMethod, array $body): Response
    {
        $assembledURL = $this->assembleURL($this->baseURL, 'bot' . $this->botToken . '/', $telegramMethod);

        $response = '';
        switch($httpMethod){
            case "GET":
                $response = Http::get($assembledURL, $body);
                break;
            case "POST":
                $response = Http::post($assembledURL, $body);
                break;
        }

        return $this->handleResponse($response);
    }

    /**
     * @return string
     */
    private function assembleURL(): string
    {
        $url = '';
        foreach(func_get_args() as $arg){
            $url .= $arg;
        }

        return $url;
    }

    /**
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    private function handleResponse(Response $response): Response
    {
        if(!$response->ok()){
            throw new Exception($response->json()['description']);
        }

        return $response;
    }

    /**
     * @return SupportCollection
     * @throws NoMessages
     * @throws Exception
     */
    public function getUpdates(): SupportCollection
    {
        $updateOffset = $this->getNextUpdateId();
        $response = $this->callTelegramAPI(
            $this->utils->getHttpMethod("toGetUpdates"),
            $this->utils->getTelegramMethod("toGetUpdates"),
            $this->bodyAssembler->assembleGetUpdatesBody($updateOffset)
        );

        $this->checkIfResultsExists($response);
        $results = $this->getResults($response);

//        $this->setLastUpdateId($results->last()['update_id']);

        return $results;
    }

    /**
     * @param Response $response
     * @throws NoMessages
     */
    private function checkIfResultsExists(Response $response){
        if(count($response['result']) == 0){
            throw new NoMessages("Não existem novas mensagens");
        }
    }

    /**
     * @param Response $response
     * @return SupportCollection
     */
    private function getResults(Response $response): SupportCollection
    {
        return collect($response['result']);
    }

    /**
     * @return int
     */
    public function getNextUpdateId(): int
    {
        $lastUpdateId = Cache::get('last_update');
        return $lastUpdateId + 1;
    }

    /**
     * @param int $lastUpdateId
     */
    public function setLastUpdateId(int $lastUpdateId): void
    {
        Cache::put('last_update', $lastUpdateId);
    }

}
