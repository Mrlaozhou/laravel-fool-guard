<?php

namespace Mrlaozhou\Guard\Commands;

use Illuminate\Console\Command;

class RollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fool-guard:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback fool-guard database migration';


    public function handle ()
    {
        $this->call( 'migrate:rollback', [
            '--path'        =>  realpath(__DIR__ . '/../../database/migrations'),
            '--realpath'    =>  true,
        ] );
    }
}