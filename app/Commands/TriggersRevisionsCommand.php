<?php

namespace App\Commands;

class TriggersRevisionsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'triggers:revisions
                            {trigger-id : The ID of the trigger}
                            {--side-load= : Retrieve related records as part of a single request. Pass a comma-separated list of available records; users}
                            {--max : Request multiple pages of trigger revisions, up to a maximum 1000}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the revisions associated with a trigger.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $revisions = array();
        $params   = array();
        $trigger_id = $this->argument( 'trigger-id' );

        if ( $this->option( 'side-load' ) ) {
            $params['include'] = explode( ',', $this->option( 'side-load' ) );
        }

        try {

            do {
                $response = $this->Zendesk->get("/api/v2/triggers/{$trigger_id}/revisions", $params );
                $revisions = array_merge( $revisions, $response->trigger_revisions );
                $params['cursor'] = $response->after_cursor;
            } while ( $response->after_cursor && $this->option( 'max' ) );

            $response->trigger_revisions = $revisions;
            $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

            return self::SUCCESS;
        } catch ( Exception $exception ) {
            $this->error( $exception->getMessage() );

            return self::FAILURE;
        }
    }

}
