<?php

namespace App\Commands;

class TargetsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'targets
                            {target-id? : The ID of the target}
                            {--max : Retrieve multiple pages of targets, maximum of 10 pages}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List targets';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $params = array( 'page' => 1 );
        $targets = array();

        try {

            if ( $this->argument( 'target-id' ) ) {
                $response = $this->Zendesk->targets()->find( $this->argument( 'target-id' ) );
            } else {

                do {
                    $response = $this->Zendesk->targets()->findAll( $params );
                    $targets = array_merge( $targets, $response->targets );
                    $params['page'] ++;
                } while ( $response->next_page && $this->option( 'max' ) && $params['page'] < 11 );

                $response->targets = $targets;
            }

            $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

            return self::SUCCESS;
        } catch ( Exception $exception ) {
            $this->error( $exception->getMessage() );

            return self::FAILURE;
        }
    }
}
