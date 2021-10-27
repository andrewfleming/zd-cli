<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class TicketsCreateManyCommand extends Command
{
    const SPACE = ' ';
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = "tickets:create:many";

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a batch of tickets';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }

}
