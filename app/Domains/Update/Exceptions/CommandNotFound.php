<?php
declare(strict_types=1);


namespace App\Domains\Update\Exceptions;


use Exception;
use Throwable;

/**
 * Class CommandNotFound
 * @package App\Domains\Update\Exceptions
 */
class CommandNotFound extends Exception
{
    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct
    (
        string $command,
        $code = 0,
        Throwable $previous = null
    )
    {
        $message = "Comando '$command' não existe";
        parent::__construct($message, $code, $previous);
    }
}
