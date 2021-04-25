<?php
declare(strict_types=1);


namespace App\Domains\Core\Interfaces;


/**
 * Interface UpdateInterface
 * @package App\Domains\Core\Interfaces
 */
interface UpdateInterface
{
    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return string
     */
    public function getSenderId(): string;

    /**
     * @return string
     */
    public function getTargetId(): string;
}
