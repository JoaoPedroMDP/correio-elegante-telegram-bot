<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Library\Api;
use App\Ecomper;
use App\Message;
use App\Helpers\{Validators, ColorHandler};
use Illuminate\Database\QueryException;

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
        // var_dump($text);
        error_log("Splits\n");
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
        $data[0] != 'start' && $text != '' ? array_push($data, $text) : null;
        // var_dump($data);
        return $data;
    }

    private function botSend($targetId, $message){
        // print 'oi';
        // Precisa ser antes para caso a pessoa ainda nao tenha se cadastrado e tente mandr mensagem.
        // Nesse caso precisamos enviar a ela uma mensagem dizendo que precisa se cadastrar
        $response = Api::sendMessage($targetId, $message, $isBot = true);
        return $response->message_id;

    }

    private function send($sender, $target, $message, $replyMessageId = null){
        error_log("send\n");
        $target = $this->searchEcomperByUser($target);
        $sender = $this->searchEcomperById($sender);
        if(!$target){
            $aux = Api::sendMessage($sender, 'Esse usuário não existe');
            return null;
        }else{
            if(isset($replyMessageId)){
                $message = 'Resposta de ' . $sender->fakeIdentifier . ":\n" . $message;
                $botMessageId = Api::sendMessage($target->chat_id, $message, $replyMessageId);
            }else{
                $message = 'Mensagem de ' . $sender->fakeIdentifier . ":\n" . $message;
                $botMessageId = Api::sendMessage($target->chat_id, $message);
            }
            if($botMessageId === -1){
                return $botMessageId;
            }
            return $botMessageId->message_id;
        }
    }

    private function newMessage($senderUsername, $target, $text, $message_id){
        try{
            $message = Message::create([
                'sender' => $senderUsername,
                'receiver' => $target,
                'text' => $text,
                'message_id' => $message_id
            ]);
        }catch(QueryException $e){
            $message = Message::create([
                'sender' => $senderUsername,
                'receiver' => $target,
                'text' => 'Mensagem era muito grande',
                'message_id' => $message_id
            ]);
        }
    }

    private function register($sender){
        if($this->searchEcomperByUser($sender['username'])){
            $this->botSend($sender['chat_id'],'*Você já está cadastrado*');
            return false;
        }
        $color = new ColorHandler;
        $data = [
            'chat_id' => $sender['chat_id'],
            'username' => $sender['username'],
            'fakeIdentifier' => $color->getColor()
        ];
        
        $ecomper = Ecomper::create($data);
        return true;
    }

    private function reply($messageId, $text){
        $message = Message::where('message_id', $messageId)->first();
        $target = $this->searchEcomperByUser($message->sender);
        $sender = $this->searchEcomperByUser($message->receiver);
        $botMessageId = $this->send($sender->chat_id, $target->username, $text, $messageId -1);
        $this->newMessage($sender->username, $target->username, $text, $botMessageId);
    }

    private function searchEcomperByUser($username){
        $ecomper = Ecomper::where('username', $username);
        if($ecomper){
            return $ecomper->first();
        }
        return null;
    }

    private function searchEcomperById($chatId){
        $ecomper = Ecomper::where('chat_id', $chatId);
        if($ecomper){
            return $ecomper->first();
        }
        return null;
    }
    // DAQUI PARTEM $THIS->SEND E $THIS->REPLY
    private function commands($command, $trio, $sender, $message){
        error_log("commands\n");
        $helper = new Validators;
        $sender = Ecomper::where('chat_id', $sender['chat_id'])->first();
        switch ($command) {
            case 'send':
                $ok = $helper->validatesSend($trio);
                if(!$ok[0]){
                    $string = $message->text;
                    $messageId = $this->botSend($sender->chat_id, "*Algo de errado não está certo\!*\n" . $ok[1] . "\n\n*O que você enviou\:* $string\n\n*Para enviar mensagens\, utilize \'/send Username Mensagem de texto\'*\n*O ESPAÇO ENTRE COMANDO \- DESTINATARIO/TEXTO \- TEXTO É OBRIGATÓRIO*");
                    $this->newMessage('CorAnteBot', 'UnregisteredUser', 'Pessoa errou no /send', $messageId);
                }else{
                    $target = $trio[1];
                    $text = $trio[2];
                    $botMessageId = $this->send(
                        $sender->chat_id,
                        $target,
                        $text
                    );
                    $botMessageId ? $this->newMessage($sender->username, $target, $text, $botMessageId) : null;
                }
            break;
            case 'reply':
                $ok = $helper->validatesReply($trio);
                if(!$ok[0]){
                    $string = $message->text; 
                    $messageId = $this->botSend($sender['chat_id'], "*Algo de errado não está certo\!*\n" . $ok[1] . "\n\n*O que você enviou\:* $string\n\n*Para responder a uma mensagem\, responda à mensagem normalmente e no texto de resposta utilize \'/reply Texto de resposta\'\n*O ESPAÇO ENTRE COMANDO \- DESTINATARIO/TEXTO \- TEXTO É OBRIGATÓRIO\n");
                    $this->newMessage('CorAnteBot', $sender['username'], 'Pessoa errou no /reply', $messageId);
                }else{
                    $text = $trio[1];
                    $targetMessage = Message::where('message_id', $message->reply_to_message->message_id)->first();
                    if(!isset($message->reply_to_message)){
                        print 'ue';
                        $messageId = $this->botSend($message->from->id, '*Você precisa responder à mensagem para que tudo ocorra bem\.*');
                        $this->newMessage('CorAnteBot', $sender['username'], 'Pessoa errou no /reply - não respondeu', $messageId);
                    }else if($targetMessage->sender == 'CorAnteBot'){
                        $messageId = $this->botSend($message->from->id, '*Namoral que tu ta falando com o bot\?*');
                        $this->newMessage('CorAnteBot', $sender['username'], 'Namoral que tu ta falando com o bot?', $messageId);
                    }else{
                        $this->reply(
                            $message->reply_to_message->message_id,
                            $text
                        );
                    }
                }
                break;
            default:
            $messageId = $this->botSend($sender['chat_id'], "*Que comando é esse que nem eu conhecia\?*\n");
            $this->newMessage('CorAnteBot', 'UnregisteredUser', 'Comando desconhecido', $messageId);
                break;
        }
    }

    private function checkIfRegistered($message){
        if($this->searchEcomperById($message->from->id)){
            return true;
        }
        return false;
    }

    private function welcomeMessage(){
        return "*Bem vindo ao bot\!\n*Para enviar mensagens\, utilize \'/send Username Mensagem de texto\'\n*Para responder a mensagens\, responda à mensagem normalmente e no texto de resposta utilize \'/reply Texto de resposta\'\n*O ESPAÇO ENTRE COMANDO \- DESTINATARIO/TEXTO \- TEXTO É OBRIGATÓRIO*\n*Aproveite sua estadia\!*";
    }

    /** Função principal da aplicação
     * Que preguiça de comentar essa porcaria
     * 
     * Envia uma request para a API do Telegram, procurando por mensagens novas
     * Caso haja mensagens novas, itera sobre o array devolvido na request e
     * toma as medidas necessárias de acordo com o conteúdo da mensagem
     */
    public function updateMessages(){
        error_log("updateMessages\n");
        $response = Api::update();
        if($response){
            foreach($response as $actual){
                $message = $actual->message;
                $userOk = $this->checkIfRegistered($message);
                if(!isset($message->from->username)){
                    $messageId = $this->botSend(
                        $message->from->id,
                        '*Poe username ae\, faz favor\.*'
                    );
                    $this->newMessage('CorAnteBot', 'UnregisteredUser', 'Poe username ae, faz favor.', $messageId);
                }else{
                    $trio = $this->splitsCommands($message->text);
                    $command = $trio[0];
                    $sender['chat_id'] = $message->from->id;
                    $sender['username'] = $message->from->username;
                    if($command == 'start'){
                        if($this->register($sender)){
                            $messageId = $this->botSend(
                                $sender['chat_id'],
                                $this->welcomeMessage()
                            );
                            $this->newMessage('CorAnteBot', 'UnregisteredUser', 'Mensagem de boas vindas', $messageId);
                        }
                    }else if($userOk){
                        $this->commands($command, $trio, $sender, $message);
                    }else{
                        $messageId = $this->botSend(
                            $message->from->id,
                            "*Se cadastra aí mandando \'/start\'\, faz favor\. Obrigado\.*"
                        ); 
                        $this->newMessage('CorAnteBot', 'UnregisteredUser', 'Se cadastra aí faz favor, obrigado.', $messageId);
                    }
                }
            }
            return response()->json('Tudo certo por enquanto :)');
        }
        return response()->json('Nada de novo debaixo do céu hoje');
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
