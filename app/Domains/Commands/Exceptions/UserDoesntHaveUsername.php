<?php
declare(strict_types=1);


namespace App\Domains\Commands\Exceptions;


use Exception;
use Throwable;

/**
 * Class UserDoesntHaveUsername
 * @package App\Domains\Commands\Exceptions
 */
class UserDoesntHaveUsername extends Exception
{
    /**
     * UserDoesntHaveUsername constructor.
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct(
        $message = "Para utilizar o bot você deve configurar um username em sua conta Telegram",
        $code = 0, Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
