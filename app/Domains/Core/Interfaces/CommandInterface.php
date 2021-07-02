<?php
declare(strict_types=1);


namespace App\Domains\Core\Interfaces;


use App\Domains\Update\Update;

/**
 * Interface CommandInterface
 * @package App\Domains\Commands
 */
interface CommandInterface
{
    public function execute();

    /**
     * @param Update $update
     */
    public static function fromUpdate(Update $update);
}
