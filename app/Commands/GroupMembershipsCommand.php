<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class GroupMembershipsCommand extends ZendeskBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'groups:memberships
                            {id? : Group Membership ID}
                            {--user-id= : List group a user is has membership of}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'You can use the API to list what agents are in which groups, and reassign group members.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): bool {
        $params = array();

        if ( $this->option( 'user-id' ) ) {
            $params['user_id'] = $this->option( 'user-id' );
        }

        try{

            if ( $this->argument( 'id' ) ) {
                $response = $this->Zendesk->groupMemberships()->find( $this->argument( 'id' ), $params );
            } else {
                $response = $this->Zendesk->groupMemberships()->findAll( $params );
            }

        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );
            return $this::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );
        return $this::SUCCESS;
    }

}
