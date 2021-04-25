<?php
declare(strict_types=1);


namespace App\Domains\User\Exceptions;


use Exception;
use Throwable;

/**
 * Class UserNotFound
 * @package App\Domains\User\Exceptions
 */
class UserNotFound extends Exception
{
    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct($message = "Usuário não encontrado", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
