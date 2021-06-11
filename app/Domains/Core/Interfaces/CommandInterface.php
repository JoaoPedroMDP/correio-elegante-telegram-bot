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
     * @return void
     */
    public function persistMessageInDatabase();
}
