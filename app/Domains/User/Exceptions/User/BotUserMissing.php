<?php
declare(strict_types=1);


namespace App\Domains\User\Exceptions\User;


use Exception;
use Throwable;

/**
 * Class BotUserMissing
 * @package App\Domains\User\Exceptions\User
 */
class BotUserMissing extends Exception
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
        $message = "Por que raios não achamos o Bot User?",
        $code = 0,
        Throwable $previous = null
    )
    {
        // TODO Essa exception é grave. Precisa adicionar alguns handlers para ela
        parent::__construct($message, $code, $previous);
    }
}
