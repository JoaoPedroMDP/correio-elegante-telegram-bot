<?php
declare(strict_types=1);


namespace App\Domains\Message\Exceptions;


use App\Domains\User\Exceptions\MessageException;

/**
 * Class UserTalkingToBot
 * @package App\Domains\Message\Exceptions
 */
class UserTalkingToBot extends MessageException
{

    /**
     * UserTalkingToBot constructor.
     */
    public function __construct( $raiserTid, $code = 400, $previous = null)
    {
        $message = "Para utilizar um comando, é necessário usar '/' antes dele, como /start, /send, /reply ...";
        parent::__construct( $raiserTid, $message, $code, $previous);
    }
}
