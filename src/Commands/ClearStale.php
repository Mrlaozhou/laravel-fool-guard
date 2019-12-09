<?php

namespace Mrlaozhou\Guard\Commands;

use Illuminate\Console\Command;

class ClearStale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fool-guard:clearStale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清除过期token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var \Mrlaozhou\Guard\Token $token */
        $token      =   app(config('fool-guard.model'));
        $result     =   $token->newQuery()->whereDate('expired_at', '<', now())
            ->delete();

        $this->alert("共删除<fg=green> {$result} </fg=green>条记录");
        return ;
    }
}
