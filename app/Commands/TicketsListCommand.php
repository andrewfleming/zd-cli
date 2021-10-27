<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class TicketsListCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tickets:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get a list of tickets';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $views   = $this->Zendesk->tickets();
        $headers = [ 'ID', 'Title' ];
        $rows    = array();

        foreach ( $views->views as $view ) {
            $rows[] = array( $view->id, $view->title );
        }

        $this->table( $headers, $rows );
    }
}
