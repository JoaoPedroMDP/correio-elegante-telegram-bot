<?php
declare(strict_types=1);

namespace App\Helpers;

/**
 * Class ColorHandler
 * @package App\Helpers
 */
class ColorHandler extends AllColors
{
    protected $used = [];

    /**
     * @return string
     */
    public function getColor(): string
    {

        $index = $this->rand();
        while($this->colorWasTaken($index)){
            $index = $this->rand();
        }

        $this->takeColor($index);
        return $this->colors[$index];
    }

    /**
     * @return int
     */
    private function rand(): int
    {
        return rand(0, count($this->colors) - 1);
    }

    /**
     * @param int $index
     * @return bool
     */
    private function colorWasTaken(int $index): bool
    {
        return isset($this->used[$index]);
    }

    /**
     * @param int $index
     */
    private function takeColor(int $index)
    {
        $this->used[$index] = true;
    }
}
