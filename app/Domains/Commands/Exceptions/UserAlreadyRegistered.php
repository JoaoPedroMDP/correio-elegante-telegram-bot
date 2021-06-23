<?php
declare(strict_types=1);


namespace App\Domains\Commands\Exceptions;


use Exception;
use Throwable;

/**
 * Class UserAlreadyRegistered
 * @package App\Domains\Commands\Exceptions
 */
class UserAlreadyRegistered extends Exception
{

    /**
     * UserAlreadyRegistered constructor.
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct(
        $message = "Não é preciso se registrar mais de uma vez",
        $code = 0, Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
