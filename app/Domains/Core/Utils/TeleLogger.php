<?php
declare(strict_types=1);


namespace App\Domains\Core\Utils;

use Illuminate\Support\Facades\Log;
/**
 * Class TeleLogger
 * @package App\Domains\Core
 */
class TeleLogger
{
    /**
     * @var string
     */
    private static $defaultChannel = 'telebot';

    /**
     * @param string $message
     * @param string $level
     * @param string|null $channel
     */
    public static function log(string $message, string $level, string $channel = null){
        $channel = is_null($channel) ? self::$defaultChannel : $channel;

        switch ($level){
            case 'error':
                Log::channel($channel)->error($message);
                break;
            case 'info':
                Log::channel($channel)->info($message);
        }
    }
}
