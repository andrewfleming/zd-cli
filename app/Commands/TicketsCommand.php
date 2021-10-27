<?php

namespace App\Commands;

class TicketsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     * @example ./zd-cli tickets --limit=2 --sort=-updated_at | jq '.tickets[] | {id, subject}'
     */
    protected $signature = 'tickets
                            {ids?* : Space-seperated list of, up to 100, ticket ID}
                            {--include= : Side-load related records as part of a single request. Pass a comma-separated list of available records; users, groups, organizations, last_audits, metric_sets, dates, sharing_agreements, comment_count, incident_counts, ticket_forms, metric_events (single ticket), slas (single ticket)}';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List recent tickets, limited to 100';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {

        if ( count( $this->argument( 'ids' ) ) > 100 ) {
            $this->error( 'Limit ID arguments to 100' );
            return self::FAILURE;
        }

        $extra_params = array();

        if ( $this->option( 'include' ) ) {
            $extra_params['include'] = explode( ',', $this->option( 'include' ) );
        }

        try{

            if ( $this->argument( 'ids' ) ) {
                $response = $this->Zendesk->tickets()->findMany( $this->argument( 'ids' ), $extra_params );
            } else {
                $response = $this->Zendesk->tickets()->findAll( $extra_params );
            }

        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );
            return self::FAILURE;
        }
        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

        return self::SUCCESS;
    }

}
