<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class TicketsCommentsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tickets:comments
                            {id : A ticket ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List a ticket\'s comments representing the conversation between requesters, collaborators, and agents.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        try {
            $response = $this->Zendesk->tickets( $this->argument('id') )->comments()->findAll();
        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );

            return self::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

        return Command::SUCCESS;
    }

}
