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
     * @return void
     */
    public function persistMessageInDatabase();
}
