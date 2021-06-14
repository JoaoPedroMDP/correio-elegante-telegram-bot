<?php
declare(strict_types=1);


namespace App\Domains\Core\Services;


use App\Domains\Message\Services\MessageServices;
use App\Domains\User\Exceptions\MessageException;
use App\Helpers\ColorHandler;
use Exception;

/**
 * Class CoreServices
 * @package App\Domains\Core\Services
 */
class CoreServices
{
    /**
     * @var MessageServices
     */
    private $messageServices;

    /**
     * @var ColorHandler
     */
    private $colorHandler;

    public function __construct(){
        $this->messageServices = new MessageServices();
        $this->colorHandler = new ColorHandler();
    }

    /**
     * @param MessageException $e
     * @throws Exception
     */
    public function handleMessageException(MessageException $e)
    {
        $this->messageServices->sendMessage(
        'bot',
        strval($e->getRaiserTid()),
        $e->getMessage()
        );
    }

    /**
     * @return string
     */
    public function generateFakIdentifier(): string
    {
        return $this->colorHandler->getColor();
    }
}
