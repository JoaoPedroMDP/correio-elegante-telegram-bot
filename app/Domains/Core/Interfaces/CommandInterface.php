<?php
declare(strict_types=1);


namespace App\Domains\Core\Interfaces;


use App\User;
use Exception;

/**
 * Interface CommandInterface
 * @package App\Domains\Commands
 */
interface CommandInterface
{
    public function execute();

    /**
     * @param Exception $exception
     */
    public function handleException(Exception $exception);

    /**
     * @return User
     */
    public function getSender(): User;


    /**
     * @param User $sender
     */
    public function setSender(User $sender): void;


    /**
     * @return User
     */
    public function getTarget(): User;


    /**
     * @param User $target
     */
    public function setTarget(User $target): void;


    /**
     * @return string
     */
    public function getMessage(): string;


    /**
     * @param string $message
     */
    public function setMessage(string $message): void;

}
