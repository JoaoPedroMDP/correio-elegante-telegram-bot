<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Library\Api;
use App\Ecomper;
use App\Message;
use App\Helpers\{Helper, Validators, ColorHandler};
use Illuminate\Database\QueryException;
use Log;

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
        return $data;
    }

    private function botSend($targetId, $message){
        // Precisa ser antes para caso a pessoa ainda nao tenha se cadastrado e tente mandar mensagem.
        // Nesse caso precisamos enviar a ela uma mensagem dizendo que precisa se cadastrar
        Api::sendMessage($targetId, $message);
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
                $message = 'Resposta de @' . $sender->fakeIdentifier . "@:\n" . $message;
                $botMessageId = Api::sendMessage($target->chat_id, $message);
            }else{
                $message = 'Mensagem de @' . $sender->fakeIdentifier . "@:\n" . $message;
                $botMessageId = Api::sendMessage($target->chat_id, $message);
            }
            if($botMessageId === -1){
                return false;
            }
            return true;
        }
    }

    private function newMessage($senderUsername, $targetUsername, $text){
        try{
            $message = Message::create([
                'sender' => $senderUsername,
                'receiver' => $targetUsername,
                'text' => $text
            ]);
        }catch(QueryException $e){
            Log::channel('telebot')->warning($senderUsername . ' mandou uma mensagem muito grande. Um placeholder será armazenado no lugar do texto');
            $message = Message::create([
                'sender' => $senderUsername,
                'receiver' => $targetUsername,
                'text' => 'Mensagem era muito grande'
            ]);
        }
    }

    private function register($sender){
        if($this->searchEcomperByUser($sender['username'])){
            $this->botSend($sender['chat_id'],'Você já está cadastrado');
            return false;
        }
        $color = new ColorHandler;
        $data = [
            'name' => $sender['name'],
            'chat_id' => $sender['chat_id'],
            'username' => $sender['username'],
            'fakeIdentifier' => $color->getColor()
        ];
        
        $ecomper = Ecomper::create($data);
        return $ecomper;
    }

    private function reply($replyTarget, $originalMessage, $text){

        $target = $this->whoSent($originalMessage);
        $sender = $this->searchEcomperByUser($replyTarget);
        $ok = $this->send($sender->chat_id, $target->username, $text);
        if($ok)
            $this->newMessage($sender->username, $target->username, $text);
    }

    private function searchEcomperByUser($username){
        $ecomper = Ecomper::where('username', $username)->first();
        if($ecomper){
            return $ecomper;
        }
        return null;
    }

    private function searchEcomperById($chatId){
        $ecomper = Ecomper::where('chat_id', $chatId)->first();
        if($ecomper){
            return $ecomper;
        }
        return null;
    }

    private function searchEcomperByColor($color){
        $ecomper = Ecomper::where('fakeIdentifier', $color)->first();
        if($ecomper){
            return $ecomper;
        }
        return null;
    }

    private function whoSent($text){
        $color= explode('@', $text)[1];
        $ecomper = $this->searchEcomperByColor($color);
        if($ecomper){
            return $ecomper;
        }
        return null;
    }
    private function allEcompers(){
        $ecompers = Ecomper::all('name', 'username');
        $stringGiven = '';
        foreach($ecompers as $ecomper){
            $stringGiven .= "\n$ecomper->name => $ecomper->username";
        }

        return $stringGiven;
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
                    $messageId = $this->botSend($sender->chat_id, "Algo de errado não está certo!\n" . $ok[1] . "\n\nO que você enviou: $string\n\nPara enviar mensagens, utilize '/send Username Mensagem de texto'*\n*O ESPAÇO ENTRE COMANDO - DESTINATARIO/TEXTO - TEXTO É OBRIGATÓRIO");
                    $this->newMessage('CorAnteBot', 'UnregisteredUser', 'Pessoa errou no /send');
                }else{
                    $target = $trio[1];
                    $text = $trio[2];
                    $this->send(
                        $sender->chat_id,
                        $target,
                        $text
                    );
                    $this->newMessage($sender->username, $target, $text);
                }
            break;
            case 'reply':
                $ok = $helper->validatesReply($trio);
                if(!$ok[0]){
                    $string = $message->text; 
                    $this->botSend($sender['chat_id'], "Algo de errado não está certo!\n" . $ok[1] . "\n\nO que você enviou: $string\n\nPara responder a uma mensagem, responda à mensagem normalmente e no texto de resposta utilize '/reply Texto de resposta'\nO ESPAÇO ENTRE COMANDO - DESTINATARIO/TEXTO - TEXTO É OBRIGATÓRIO\n");
                    $this->newMessage('CorAnteBot', $sender['username'], 'Pessoa errou no /reply');
                }else{
                    $text = $trio[1];
                    if(!isset($message->reply_to_message)){
                        $this->botSend($message->from->id, 'Você precisa responder à mensagem para que tudo ocorra bem.');
                        $this->newMessage('CorAnteBot', $sender['username'], 'Pessoa errou no /reply - não respondeu');
                    }else if($this->whoSent($message->reply_to_message->text) == 'CorAnteBot'){
                        $this->botSend($message->from->id, 'Namoral que tu ta falando com o bot\?');
                        $this->newMessage('CorAnteBot', $sender['username'], 'Namoral que tu ta falando com o bot?');
                    }else{
                        $this->reply(
                            $message->from->username,
                            $message->reply_to_message->text,
                            $text
                        );
                    }
                }
                break;
            default:
            $this->botSend($sender['chat_id'], "Que comando é esse ($command) que nem eu conhecia?\n");
            $this->newMessage('CorAnteBot', 'UnregisteredUser', 'Comando desconhecido');
                break;
        }
    }

    private function checkIfRegistered($message){
        if($this->searchEcomperById($message->from->id)){
            return true;
        }
        return false;
    }

    private function welcomeMessage($color){
        return "Bem vindo ao bot! Sua cor é @$color@\nPara enviar mensagens, utilize '/send Username Mensagem de texto'\nPara responder a mensagens, responda à mensagem normalmente e no texto de resposta utilize '/reply Texto de resposta'\nO ESPAÇO ENTRE COMANDO - DESTINATARIO/TEXTO - TEXTO É OBRIGATÓRIO\nNOTA: Todas as mensagens enviadas pelo bot serão gravadas no BD do operador do bot (Alguem de DH)\nAproveite sua estadia!";
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
                        'Poe username ae, faz favor.'
                    );
                    $this->newMessage('CorAnteBot', 'UnregisteredUser', 'Poe username ae, faz favor.');
                }else{
                    $trio = $this->splitsCommands($message->text);
                    $command = $trio[0];
                    $sender = Helper::setSender($message);
                    if($command == 'start'){
                        $newEcomper = $this->register($sender);
                        if($newEcomper){
                            $this->botSend(
                                $newEcomper->chat_id,
                                $this->welcomeMessage($newEcomper->fakeIdentifier)
                            );
                            $this->newMessage('CorAnteBot', $newEcomper->username, 'Mensagem de boas vindas');
                        }
                    }else if($userOk && $command == 'ecompers'){
                        $aux = $this->allEcompers();
                        $this->botSend(
                            $sender['chat_id'],
                            $aux
                        );
                        $this->newMessage('CorAnteBot', $sender['username'], 'Mostrando todos os ecompers');
                    }else if($userOk){
                        $this->commands($command, $trio, $sender, $message);
                        Log::channel('telebot')->info('Comando '. $trio[0] .' de '. $sender['username'] .' processado. Sem erros.');
                    }else{
                        $messageId = $this->botSend(
                            $message->from->id,
                            "Se cadastra aí mandando '/start', faz favor. Obrigado."
                        ); 
                        $this->newMessage('CorAnteBot', 'UnregisteredUser', 'Se cadastra aí faz favor, obrigado.');
                    }
                }
            }
        }else{
            Log::channel('telebot')->info('Nada de novo embaixo do céu. Nenhuma mensagem nova, também.');
        }
    }
}
