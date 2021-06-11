<?php
declare(strict_types=1);


namespace App\Domains\Core\Services;


use App\Domains\Message\Services\MessageServices;
use App\Domains\User\Exceptions\MessageException;
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

    public function __construct(){
        $this->messageServices = new MessageServices();
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

    public function generateFakIdentifier()
    {
    }
}
