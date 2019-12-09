<?php

namespace Mrlaozhou\Guard\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;

class MigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fool-guard:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrating fool-guard database .';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('migrate', [
            '--path' => realpath(__DIR__ . '/../../database/migrations'),
            '--realpath' => true
        ]);
    }
}