<?php
declare(strict_types=1);


namespace App\Domains\Commands\Exceptions;


use Exception;
use Throwable;

/**
 * Class UserDidNotRepliedToMessageManually
 * @package App\Domains\Commands\Exceptions
 */
class UserDidNotRepliedToMessageManually extends Exception
{
    /**
     * UserDidNotRepliedToMessageManually constructor.
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct(
        string $message = "Para responder à uma mensagem no bot, você deve responder a ela utilizando o recurso de reply do telegram",
        int $code = 0, Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
