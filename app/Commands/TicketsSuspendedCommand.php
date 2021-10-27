<?php

namespace App\Commands;

class TicketsSuspendedCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tickets:suspended
                            {ticket-id? : Suspended ticket ID}
                            {--sort-by= : The field to sort the ticket by, being one of author_email, cause, created_at, or subject.}
                            {--sort-order= : The order in which to sort the suspended tickets. This can take value asc or desc.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $query_params = array_filter( array(
            'sort_by'    => $this->option( 'sort-by' ),
            'sort_order' => $this->option( 'sort-order' )
        ) );

        try {

            if ( $this->argument( 'ticket-id' ) ) {
                $response = $this->Zendesk->suspendedTickets()->find( $this->argument( 'ticket-id' ) );
            } else {
                $response = $this->Zendesk->suspendedTickets()->findAll( $query_params );
            }

        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );

            return self::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

        return self::SUCCESS;
    }
}
