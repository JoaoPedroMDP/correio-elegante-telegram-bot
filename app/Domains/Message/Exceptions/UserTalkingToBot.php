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
    public function __construct($code = 400, $previous = null)
    {
        $message = "Para utilizar um comando, é necessário usar '/' antes dele, como /start, /send, /reply ...";
        parent::__construct($message, $code, $previous);
    }
}