<?php

namespace App\Commands;

class TicketAuditsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tickets:audits
                            {ticket_id : Ticket ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Each audit represents a single update to the ticket. An update can consist of one or more events. https://developer.zendesk.com/rest_api/docs/support/ticket_audits';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        try {
            $response = $this->request_audits( $this->argument( 'ticket_id' ) );
        } catch ( \Exception $exception ) {
            $this->line( $exception->getMessage() );

            return self::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

        return self::SUCCESS;
    }

    /**
     * Retrieve ticket audit events
     *
     * @param int $ticket_id
     *
     * @return array
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    private function request_audits( int $ticket_id ): array {
        $params = array( 'ticket_id' => $ticket_id );
        return $this->Zendesk->tickets()->audits()->findAll( $params )->audits;
    }

}
