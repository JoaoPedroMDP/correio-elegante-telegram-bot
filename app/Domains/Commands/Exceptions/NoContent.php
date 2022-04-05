<?php
declare(strict_types=1);


namespace App\Domains\Commands\Exceptions;


use Exception;
use Throwable;

/**
 * Class NoContent
 * @package App\Domains\Commands\Exceptions
 */
class NoContent extends Exception
{
    /**
     * NoContent constructor.
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct(
        string $message = "Você enviou uma mensagem em branco :|",
        int $code = 0, Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
