<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Library\Api;
use App\Ecomper;

class CorAnteController extends Controller
{
    private function retrieveTrio($text){
        $command = str_replace('/','',$text);
        $data = explode('~', $command);
        return $data;
    }

    public function sendManual(Request $request){
        $target = '-'.$request->chat_id;
        $response = Api::sendMessage($target, $request->text);
        return response()->json($response);
    }

    private function send($target, $message, $photo_id = null){
        $alvo = Ecomper::where('username', $target)->first();
        $alvo = '-'.$alvo->group_id;
        $photo_id ? Api::sendPhoto($alvo, $photo_id, $message):Api::sendMessage($alvo, $message);
    }

    public function updateMessages(){
        $response = Api::update();
        if($response){
            foreach($response as $i){
                if(isset($i->message->photo)){
                    $data = $this->retrieveTrio($i->message->caption);
                    $this->send('JohnCalvino', $data[2],$i->message->photo[0]->file_id);
                }else if(isset($i->message->entities)){
                    $data = $this->retrieveTrio($i->message->text);
                    $command = $data[0];
                    $this->$command($data[1], $data[2]);
                    dd($data);
                }
            }
        }
    }

    public function register($id, $username){
        $data = ['group_id' => $id,'username' => $username];
        $ecomper = Ecomper::create($data);
        return response()->json(['Mensagem' => 'Ecomper criado com sucesso!', 'Dado' => $ecomper]);
    }

    
}
