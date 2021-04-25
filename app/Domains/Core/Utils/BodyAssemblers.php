<?php
declare(strict_types=1);


namespace App\Domains\Core\Utils;


/**
 * Class BodyAssemblers
 * @package App\Domains\Core
 */
class BodyAssemblers
{
    /**
     * @param int $targetId
     * @param string $message
     * @return array
     */
    public function assembleSendMessageBody(int $targetId, string $message): array
    {
        return [
            "chat_id" => $targetId,
            "text" => $message
        ];
    }

    /**
     * @param int $updateOffset
     * @return array
     */
    public function assembleGetUpdatesBody(int $updateOffset): array
    {
        return [
            "offset" => $updateOffset
        ];
    }
}
