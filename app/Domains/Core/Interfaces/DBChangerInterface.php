<?php
declare(strict_types=1);


namespace App\Domains\Core\Interfaces;


/**
 * Interface DBChangerInterface
 * @package App\Domains\Core\Interfaces
 */
interface DBChangerInterface
{
    /**
     * @param array $data
     */
    public static function fromArray(array $data);

    /**
     * @return array
     */
    public function toArray(): array;
}
