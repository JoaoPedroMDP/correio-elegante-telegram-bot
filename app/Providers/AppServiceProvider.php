<?php

namespace App\Providers;

use App\Domains\Commands\SendCommand;
use App\Domains\Commands\StartCommand;
use App\Domains\Core\Interfaces\CommandInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $toBind = [
            [
                "interface" => CommandInterface::class,
                "implementation" => SendCommand::class
            ],
            [
                "interface" => CommandInterface::class,
                "implementation" => StartCommand::class
            ],
        ];

        foreach($toBind as $item)
        {
            $this->app->bind($item['interface'], $item['implementation']);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
