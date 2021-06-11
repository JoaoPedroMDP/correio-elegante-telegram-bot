<?php
declare(strict_types=1);


namespace App\Domains\Core\Utils;


/**
 * Class Utils
 * @package App\Domains\Core
 */
class Utils
{
    /**
     * @var string[]
     */
    private $httpMethods = [
        "toSendMessage" => "GET",
        "toGetUpdates" => "GET"
    ];

    /**
     * @var string[]
     */
    private $telegramMethods = [
        "toSendMessage" => "sendMessage",
        "toGetUpdates" => "getUpdates"
    ];

    /**
     * @param string|null $action
     * @return array|string
     */
    public function getHttpMethod(?string $action)
    {
        return isset($action) ? $this->httpMethods[$action] : $this->httpMethods;
    }

    /**
     * @param string[] $httpMethods
     */
    public function setHttpMethods(array $httpMethods): void
    {
        $this->httpMethods = $httpMethods;
    }

    /**
     * @param string|null $action
     * @return array|string
     */
    public function getTelegramMethod(?string $action)
    {
        return isset($action) ? $this->telegramMethods[$action] : $this->telegramMethods;
    }

    /**
     * @param array $telegramMethods
     */
    public function setTelegramMethods(array $telegramMethods): void
    {
        $this->telegramMethods = $telegramMethods;
    }

}
