<?php
declare(strict_types=1);


namespace App\Domains\User\Exceptions\User;


use Exception;
use Throwable;

/**
 * Class UserCouldNotBeCreated
 * @package App\Domains\User\Exceptions\User
 */
class UserCouldNotBeCreated extends Exception
{

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct
    (
        array $data = null,
        $message = "Usuário não pôde ser criado",
        $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
