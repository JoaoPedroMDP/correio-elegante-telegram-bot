<?php
declare(strict_types=1);


namespace App\Domains\Message\Exceptions;


use Exception;
use Throwable;

/**
 * Class MessageNotFound
 * @package App\Domains\Message\Exceptions
 */
class MessageNotFound extends Exception
{
    /**
     * NoContent constructor.
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct(
        string $message = "A mensagem a ser respondida não foi encontrada no banco de dados",
        int $code = 0, Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
