<?php

namespace App\Commands;

class TicketsTagsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tickets:tags
                            {id : Ticket ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List a ticket\'s tags';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $ticket_id = $this->argument( 'id' );

        try {
            $response = $this->Zendesk->tickets( $ticket_id )->tags()->find( $ticket_id );
        } catch ( \Exception $exception ) {
            $this->line( $exception->getMessage() );

            return self::FAILURE;
        }

        $this->line( $response->tags );

        return self::SUCCESS;
    }

}
