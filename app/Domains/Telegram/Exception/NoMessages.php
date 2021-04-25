<?php
declare(strict_types=1);


namespace App\Domains\Telegram\Exception;


use Exception;
use Throwable;

/**
 * Class NoMessages
 * @package App\Domains\Telegram\Exception
 */
class NoMessages extends Exception
{
    /**
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
