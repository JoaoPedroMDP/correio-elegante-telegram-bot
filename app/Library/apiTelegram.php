<?php

namespace App\Library;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class Api{
    private static $baseURL = 'https://api.telegram.org/bot';
    
    public static function sendMessage($target, $message){
        $token = config('services.Telegram')['token'].'/';
        $method = 'sendMessage';
        $response = Http::get(Api::$baseURL.$token.$method, [
            'chat_id' => $target,
            'text' => $message
            ]);
        $response = json_decode($response);
        return $response;
    }
    
    public static function sendPhoto($target, $id, $caption = null){
        $token = config('services.Telegram')['token'].'/';
        $method = 'sendPhoto';
        $response = Http::get(Api::$baseURL.$token.$method, [
            'chat_id' => $target,
            'photo' => $id,
            'caption' => $caption
            ]);
        $response = json_decode($response);
        return $response;
    }

    public static function update(){
        $token = config('services.Telegram')['token'].'/';
        $method = 'getUpdates';
        $lastUID = Cache::pull('last_update');
        $response = Http::get(Api::$baseURL.$token.$method, [
            'offset' => $lastUID
        ]);
        $response = collect(json_decode($response));
        if($response['result']){
            $response = json_decode($response);
            $uID = collect($response->result)->last()->update_id;
            $uID++;
            Cache::put('last_update', $uID);
            return collect($response->result);
        }
        return null;
    }
}