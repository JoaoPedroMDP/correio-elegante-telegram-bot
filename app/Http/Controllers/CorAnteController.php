<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Library\Api;
use App\Ecomper;
use App\Message;

class CorAnteController extends Controller
{
    /** Recebe a string enviada pelo usuário e a separa em partes
     * Se for /send, recebe algo como '/send DESTINATARIO MENSAGEM'
     * e retorna um array [COMANDO, DESTINATARIO, MENSAGEM]
     * 
     * Se for /reply, recebe algo como '/reply TEXTO'
     * e retorna um array [COMANDO, TEXTO]
     * 
     * O primeiro conjunto de caracteres antes do primeiro espaço
     * será sempre considerado como COMANDO e colocado na primeira posição do array de resposta
     * 
     * Caso o COMANDO seja reply, tudo a partir da posição [1] do array será considerado como
     * TEXTO e concatenado em uma única string
     * 
     * Senão, o segundo conjunto de caracteres é considerado DESTINATÁRIO e terá um espaço
     * reservado na posição [1] do array de resposta
     * @param string $text
     * @return array $data
    */
    private function splitsCommands($text){
        $data = explode(' ', $text);
        // Remove a '/' do COMANDO
        $data[0] = str_replace('/','',$data[0]);
        $text = '';
        // TERNARIA: Verifica se o COMANDO é reply
        $start = $data[0] == 'reply' ? 1 : 2;
        // Concatena o TEXTO em uma variável
        for( $i = $start ; $i < count($data) ; $i++ ){
            $text .= $data[$i].' ';
        }
        // Remove todo o resto do array. O início da remoção varia
        // de acordo com o COMANDO
        array_splice($data, $start);
        // Se o COMANDO for 'start', não existem outros argumentos,
        // portanto preciso enviar apenas o 'start'
        $data[0] != 'start' ? array_push($data, $text) : null;
        return $data;
    }

    private function send($sender, $target, $message, $photo_id = null){
        $alvo = Ecomper::where('username', $target)->first();
        if(!$alvo){
            Api::sendMessage($sender, 'Esse usuário não existe');
            return null;
        }else{
            $botMessageId = $photo_id ? Api::sendPhoto($alvo->chat_id, $photo_id, $message):Api::sendMessage($alvo->chat_id, $message);
            return $botMessageId->message_id;
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

    private function register($username, $id){
        if($this->searchEcomper($username)){
            $this->send('Bot', $username,'Tá querendo bugar o bot pô? Precisa se registrar duas veiz não.');
            return response()->json('KKK maluco queria se registrar duas veiz, azideia.');
        }
        $data = ['chat_id' => $id,'username' => $username];
        
        $ecomper = Ecomper::create($data);
        return response()->json(['Mensagem' => 'Ecomper criado com sucesso!', 'Dado' => $ecomper]);
    }

    private function reply($messageId, $text, $photo_id = null){
        $message = Message::where('message_id', $messageId)->first();
        $target = $this->searchEcomper($message->sender);
        $sender = $this->searchEcomper($message->receiver);
        $botMessageId = $this->send($sender->chat_id, $target->username, $text, $photo_id ? $photo_id : null);
        $this->newMessage($sender->username, $target->username, $text, $botMessageId);
    }

    private function searchEcomper($username){
        $ecomper = Ecomper::where('username', $username);
        if($ecomper){
            return $ecomper->first();
        }
        return null;
    }

    private function commands($command, $trio, $sender,$message){
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
                $botMessageId ? $this->newMessage($sender[1], $target, $text, $botMessageId) : null;
            break;
            case 'reply':
                $text = $trio[1];
                $this->reply(
                    $message->reply_to_message->message_id,
                    $text,
                    isset($message->photo) ? $message->photo[1]->file_id : null
                );
                break;
            default:
                print('Fala ae que alguém usou um comando errado, isso se usou comando né.');
                break;
        }
    }

    /** Função principal da aplicação
     * Que preguiça de comentar essa porcaria
     * 
     * Envia uma request para a API do Telegram, procurando por mensagens novas
     * Caso haja mensagens novas, itera sobre o array devolvido na request e
     * toma as medidas necessárias de acordo com o conteúdo da mensagem
     */
    public function updateMessages(){
        $response = Api::update();
        if($response){
            foreach($response as $actual){
                $message = $actual->message;
                // TERNARIA: Se houver o campo 'caption', significa que estamos trabalhando
                // com uma imagem, e alguns nomes mudam
                $trio = isset($message->caption) ? $this->splitsCommands($message->caption) : $this->splitsCommands($message->text);
                $command = $trio[0];
                $sender[0] = $message->from->id;
                $sender[1] = $message->from->username;
                if(count($trio) > 1){
                    $this->commands($command, $trio, $sender, $message);
                }else if($command == 'start'){
                    $this->register($message->from->username, $message->from->id);
                }
            }
            return response()->json('Tudo certo por enquanto :)');
        }
    }

    /** Serve pra caso a pessoa operando o bot queira mandar uma mensagem */
    public function sendManual(Request $request){
        if($request->chat_id == '*'){
            $ecompers = Ecomper::all();
            $data = [];
            foreach($ecompers as $ecomper){
                $target = $ecomper->chat_id;
                $response = $dataApi::sendMessage($target, $request->text);
                $aux = [
                    'Nome' => $ecomper->username,
                    'Status' => $response->ok ? 'Sucesso' : 'Algo de errado não estava certo'
                ];
                array_push($data, $aux);
            }
            return response()->json([
                'Mensagem' => 'Parabéns você acaba de floodar a empresa! -10 pontos para a Camila',
                'Dados' => $data
            ], 200); 
        }
        $target = $request->chat_id;
        $response = Api::sendMessage($target, $request->text);
        return response()->json($response);
    }
}
