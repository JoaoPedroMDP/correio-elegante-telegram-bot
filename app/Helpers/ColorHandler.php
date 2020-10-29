<?php

namespace App\Helpers;

use App\Helpers\AllColors;

class ColorHandler extends AllColors{
    protected $used = [];

    public function getColor(){
        $index = rand(0, count($this->colors));
        while(in_array($index, $this->used)){
            $index = rand(0, count($this->colors));
        }
        array_push($this->used, $index);
        return $this->colors[$index];
    }
}