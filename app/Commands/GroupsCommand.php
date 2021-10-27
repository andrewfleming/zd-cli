<?php

namespace App\Commands;

class GroupsCommand extends ZendeskBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'groups
                            {id? : Group IDs}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get groups which Zendesk agents are organized into.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): bool {

        try {

            if ( $this->argument( 'id' ) ) {
                $response = $this->Zendesk->groups()->find( $this->argument( 'id' ) );
            } else {
                $response = $this->Zendesk->groups()->findAll();
            }

        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );
            return $this::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );
        return $this::SUCCESS;
    }

}
