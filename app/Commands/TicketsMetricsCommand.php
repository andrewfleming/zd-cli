<?php

namespace App\Commands;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class TicketsMetricsCommand extends ZendeskBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tickets:metrics
                            {ticket-id? : Retrieve metrics for a ticket by ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve ticket metrics';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle():int {
        $metrics = array();
        $params = array( 'page' => 1 );
        $ticket_id = abs( $this->argument( 'ticket-id' ) );

        try {

            if ( $ticket_id ) {
                $response = $this->Zendesk->tickets( $ticket_id )->metrics()->find( $ticket_id );
            } else {

                do {
                    $response = $this->Zendesk->tickets()->metrics()->findAll();
                    $metrics = array_merge( $metrics, $response->ticket_metrics );
                    $params['page'] ++;
                } while ( $response->next_page && $params['page'] < 11 );

                $response->ticket_metrics = $metrics;
            }

            $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

            return self::SUCCESS;
        } catch ( Exception $exception ) {
            $this->error( $exception->getMessage() );

            return self::FAILURE;
        }
    }

}
