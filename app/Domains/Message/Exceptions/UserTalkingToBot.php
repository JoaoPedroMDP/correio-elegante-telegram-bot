<?php
declare(strict_types=1);


namespace App\Domains\Message\Exceptions;


use Exception;

/**
 * Class UserTalkingToBot
 * @package App\Domains\Message\Exceptions
 */
class UserTalkingToBot extends Exception
{

    /**
     * UserTalkingToBot constructor.
     */
    public function __construct
    (
        string $message = "Para utilizar um comando, é necessário usar '/' antes dele, como /start, /send, /reply ...",
        $code = 400,
        $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
