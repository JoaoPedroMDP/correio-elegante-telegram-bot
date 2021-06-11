<?php
declare(strict_types=1);


namespace App\Domains\User\Exceptions;


use Exception;
use Throwable;

/**
 * Class MessageException
 * @package App\Domains\User\Exceptions
 */
class MessageException extends Exception
{
    /**
     * @var string
     */
    public $raiserTid;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct( $raiserTid, $message, $code = 0, Throwable $previous = null)
    {
        $this->raiserTid = $raiserTid;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getRaiserTid(): string
    {
        return $this->raiserTid;
    }

    /**
     * @param string $raiserTid
     */
    public function setRaiserTid(string $raiserTid): void
    {
        $this->raiserTid = $raiserTid;
    }

}
