<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetKubovichCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resetKubovichCount:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command reset today_count kubovich table';

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
     * @return int
     */
    public function handle()
    {
        $result = DB::table('kubovich')->update(['today_count' => 0]);
    }
}
