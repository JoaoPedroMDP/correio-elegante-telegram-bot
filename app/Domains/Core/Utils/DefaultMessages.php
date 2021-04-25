<?php
declare(strict_types=1);


namespace App\Domains\Core\Utils\Utils;


/**
 * Class DefaultMessages
 * @package App\Domains\Core
 */
class DefaultMessages
{
    // Com substituição
    private static $basicGreeting = "Bem vindo ao bot! Sua cor é @%s@\n";
    private static $unknownCommand = "Que comando é esse (%s) que nem eu conhecia?\n";
    private static $whatYouSent = "O que você enviou: %s\n";

    private static $howToSendMessage = "Para enviar mensagens, utilize '/send Username Mensagem de texto'\n";
    private static $howToReply = "Para responder a mensagens, responda à mensagem normalmente e no texto de resposta utilize '/reply Texto de resposta'\n";
    private static $spaceMandatoryWarning = "O ESPAÇO ENTRE COMANDO - DESTINATÁRIO/TEXTO - TEXTO É OBRIGATÓRIO\n";
    private static $messagesWillBeRecordedWarning = "NOTA: Todas as mensagens enviadas pelo bot serão gravadas no BD do operador do bot (Alguem de DH)\n";
    private static $enjoy = "Aproveite sua estadia!";
    // Erros
    private static $mustReply = "Você precisa responder à mensagem para que tudo ocorra bem.";
    private static $somethingWrongIsNotRight = "Algo de errado não está certo!\n";
    private static $repliedBot = "Namoral que tu ta falando com o bot?\n";
    private static $unregisteredUser = "Se cadastra aí mandando '/start', faz favor. Obrigado :)";
    private static $noUsername = "Poe username ae, faz favor.";

    /**
     * @param string $color
     * @return string
     */
    public static function greetNewUser(string $color): string
    {
        $basicGreetingWithColor = self::replaceWithData(self::$basicGreeting, [$color]);

        return $basicGreetingWithColor .
            self::$howToSendMessage .
            self::$howToReply .
            self::$spaceMandatoryWarning .
            self::$messagesWillBeRecordedWarning .
            self::$enjoy;
    }

    /**
     * @param string $subject
     * @param array $replaceData
     * @return string
     */
    private static function replaceWithData(string $subject, array $replaceData): string
    {
        return vsprintf($subject, $replaceData);
    }
}
