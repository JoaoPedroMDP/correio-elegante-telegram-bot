<?php

namespace App\Helpers;

class Helper{
    public static function setSender($message){
        $sender = [];

        
        if(isset($message->from->first_name)){
            $sender['name'] = $message->from->first_name;
        }
        
        if(isset($message->from->last_name)){
            $sender['name'] .= ' ' . $message->from->last_name;
        }

        $sender['chat_id'] = $message->from->id;
        $sender ['username'] = $message->from->username;
        return $sender;
    }
}