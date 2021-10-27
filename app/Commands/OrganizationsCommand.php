<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Zendesk\API\Exceptions\MissingParametersException;

class OrganizationsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'organizations
                            {org-id? : ID of the Organization you want to retrieve}
                            {--max : Request multiple pages triggers, up to a maximum 1000}';

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
        $organizations = array();
        $params   = array(
            'page'       => 1,
        );

        try {

            if ( $this->argument( 'org-id' ) ) {
                $response = $this->Zendesk->organizations()->find( $this->argument( 'org-id' ) );
            } elseif ( $this->option( 'max' ) ) {

                do {
                    $response = $this->Zendesk->organizations()->findAll( $params );
                    $organizations = array_merge( $organizations, $response->organizations );
                    $params['page'] ++;
                } while ( $response->next_page && $this->option( 'max' ) && $params['page'] < 11 );

                $response->organizations = $organizations;
            } else {
                $response = $this->Zendesk->organizations()->findAll( $params );
            }

        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );

            return Command::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

        return Command::SUCCESS;
    }

}
