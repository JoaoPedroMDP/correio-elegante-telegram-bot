<?php
declare(strict_types=1);


namespace App\Domains\Core\RootClasses;


use App\Domains\Commands\Services\CommandServices;
use App\Domains\Core\Services\CoreServices;
use App\Domains\Core\Services\ValidatorServices;
use App\Domains\Message\Persistence\MessageRepository;
use App\Domains\Message\Services\MessageServices;
use App\Domains\Telegram\Services\TelegramServices;
use App\Domains\User\Persistence\UserRepository;
use App\Domains\User\Services\UserServices;

/**
 * Class ServicesAndRepositories
 * @package App\Domains\Core\RootClasses
 */
class ServicesAndRepositories
{
    /**
     * @var MessageServices
     */
    private $messageServices;

    /**
     * @var UserServices
     */
    private $userServices;

    /**
     * @var CommandServices
     */
    private $commandServices;

    /**
     * @var CoreServices
     */
    private $coreServices;

    /**
     * @var ValidatorServices
     */
    private $validatorServices;

    /**
     * @var TelegramServices
     */
    private $telegramServices;

    /**
     * @return MessageServices
     */
    protected function messageServices(): MessageServices
    {
        if(is_null($this->messageServices))
        {
            $this->messageServices = new MessageServices();
        }
        return $this->messageServices;
    }

    /**
     * @return UserServices
     */
    protected function userServices(): UserServices
    {
        if(is_null($this->userServices))
        {
            $this->userServices = new UserServices();
        }
        return $this->userServices;
    }

    /**
     * @return CommandServices
     */
    protected function commandServices(): CommandServices
    {
        if(is_null($this->commandServices))
        {
            $this->commandServices = new CommandServices();
        }
        return $this->commandServices;
    }

    /**
     * @return CoreServices
     */
    protected function coreServices(): CoreServices
    {
        if(is_null($this->coreServices))
        {
            $this->coreServices = new CoreServices();
        }
        return $this->coreServices;
    }

    /**
     * @return ValidatorServices
     */
    protected function validatorServices(): ValidatorServices
    {
        if(is_null($this->validatorServices))
        {
            $this->validatorServices = new ValidatorServices();
        }
        return $this->validatorServices;
    }

    /**
     * @return TelegramServices
     */
    protected function telegramServices(): TelegramServices
    {
        if(is_null($this->telegramServices))
        {
            $this->telegramServices = new TelegramServices();
        }
        return $this->telegramServices;
    }

    // REPOSITORIES REPOSITORIES REPOSITORIES REPOSITORIES REPOSITORIES REPOSITORIES REPOSITORIES
    // REPOSITORIES REPOSITORIES REPOSITORIES REPOSITORIES REPOSITORIES REPOSITORIES REPOSITORIES

    /**
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @return MessageRepository
     */
    protected function messageRepository(): MessageRepository
    {
        if(is_null($this->messageRepository))
        {
            $this->messageRepository = new MessageRepository();
        }
        return $this->messageRepository;
    }

    /**
     * @return UserRepository
     */
    protected function userRepository(): UserRepository
    {
        if(is_null($this->userRepository))
        {
            $this->userRepository = new UserRepository();
        }
        return $this->userRepository;
    }
}
