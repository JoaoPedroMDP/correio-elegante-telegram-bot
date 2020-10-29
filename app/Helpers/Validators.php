<?php

namespace App\Helpers;

class Validators{

    public function validatesSend($trio){
        $lackingData = [];
        if(count($trio) < 3){
            $data = array(false);
            if(!isset($trio[3]))
                array_push($lackingData, "\n    Falta Mensagem");
            if(!isset($trio[2]))
                array_push($lackingData, "\n    Falta Username");
            $aux = $this->lackingData($lackingData);
            array_push($data,$aux);
            return $data;
        }
        return array(true);
    }

    public function validatesReply($trio){
        if(!isset($trio[1])){
            $data = array(false);
            array_push($data, "\nFalta texto de resposta!");
            return $data;
        }
        return array(true);
    }

    private function lackingData($lackingData){
        $string = '';
        foreach($lackingData as $data){
            $string .= $data;
        }
        return $string;
    }
}