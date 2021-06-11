<?php
declare(strict_types=1);


namespace App\Domains\User\Exceptions\Message;

use App\Domains\User\Exceptions\MessageException;
use Throwable;

/**
 * Class SenderNotFound
 * @package App\Domains\User\Exceptions
 */
class SenderNotFound extends MessageException
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
        $message = "Remetente não encontrado no banco de dados",
        $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
