<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Library\Api;
use App\Ecomper;
use App\Message;

class CorAnteController extends Controller
{
    /** Retorna o trio comando-destino-mensagem
     * @param string $text
     * @return array $data
    */
    private function retrieveTrio($text){
        $command = str_replace('/','',$text);
        $data = explode(' ', $command);
        $text = '';
        $start = $data[0] == 'reply' ? 1 : 2;
        for( $i = $start ; $i < count($data) ; $i++ ){
            $text .= $data[$i].' ';
        }
        array_splice($data, $start);
        $data[0] != 'start' ? array_push($data, $text) : null;
        return $data;
    }

    public function sendManual(Request $request){
        $target = '-'.$request->chat_id;
        $response = Api::sendMessage($target, $request->text);
        return response()->json($response);
    }

    private function send($sender, $target, $message, $photo_id = null){
        $alvo = Ecomper::where('username', $target)->first();
        if(!$alvo){
            Api::sendMessage($sender, 'Esse usuário não existe');
        }else{
            $botMessageId = $photo_id ? Api::sendPhoto($alvo->chat_id, $photo_id, $message):Api::sendMessage($alvo->chat_id, $message);
            return $botMessageId->message_id;
        }
    }

    public function updateMessages(){
        $response = Api::update();
        // dd($response);
        if($response){
            foreach($response as $actual){
                $message = $actual->message;
                $trio = isset($message->caption) ? $this->retrieveTrio($message->caption) : $this->retrieveTrio($message->text);
                $command = $trio[0];
                $sender[0] = $message->from->id;
                $sender[1] = $message->from->username;
                if(count($trio) > 1){
                    switch ($command) {
                        case 'send':
                            $target = $trio[1];
                            $text = $trio[2];
                            $botMessageId = $this->send(
                                $sender[0],
                                $target,
                                $text, 
                                isset($message->photo) ? $message->photo[1]->file_id : null
                            );
                            $this->newMessage($sender[1], $target, $text, $botMessageId);
                        break;
                        case 'reply':
                            $text = $trio[1];
                            $this->reply($message->reply_to_message->message_id, $text);
                            break;
                        default:
                            print('vixe');
                            break;
                    }
                }/*else if($message){
                }*/else if($command == 'start'){
                    $this->register($message->from->username, $message->from->id);
                }
            }
        }
    }

    private function newMessage($sender, $target, $text, $message_id){
        $message = Message::create([
            'sender' => $sender,
            'receiver' => $target,
            'text' => $text,
            'message_id' => $message_id
        ]);
    }

    public function register($username, $id){
        $data = ['chat_id' => $id,'username' => $username];
        $ecomper = Ecomper::create($data);
        return response()->json(['Mensagem' => 'Ecomper criado com sucesso!', 'Dado' => $ecomper]);
    }

    private function reply($messageId, $text){
        $message = Message::where('message_id', $messageId)->first();
        $target = Ecomper::where('username',$message->sender)->first();
        $sender = Ecomper::where('username',$message->receiver)->first();
        $botMessageId = $this->send($sender->chat_id, $target->username, $text);
        $this->newMessage($sender->username, $target->username, $text, $botMessageId);
    }
}
