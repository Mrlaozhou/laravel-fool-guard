<?php
namespace Mrlaozhou\Guard;

use Illuminate\Support\Facades\Auth;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishConfig();
        //  终端下
        if( $this->app->runningInConsole() ) {
            //  注册命令
            $this->commands( [
                Commands\MigrateCommand::class,
                Commands\RollbackCommand::class,
                Commands\ClearStale::class
            ] );
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fool-guard.php', 'fool-guard' );

        Auth::extend('fool', function ($app, $name, array $config) {
            return new FoolGuard($app, $config, new GuardProvider());
        });
    }

    /**
     * publish config file
     */
    protected function publishConfig ()
    {
        $this->publishes( [
            __DIR__ . '/../config/fool-guard.php'  =>  config_path( 'fool-guard.php' )
        ], 'config' );
    }

    protected function registerMigrations ()
    {
        //  注册migrations文件
        $this->loadMigrationsFrom( __DIR__ . '/../database/migrations' );
    }

}