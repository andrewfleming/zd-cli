<?php

namespace App\Commands;

class UsersGroupsMembershipsDeleteCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'users:groups:memberships:delete
                            {user-id : User ID}
                            {group-membership-id : The group membership ID (the relationship record ID)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove a user from a group';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $response = $this->Zendesk->users( $this->argument( 'user-id' ) )->groups()->memberships()->delete( $this->argument( 'group-membership-id' ) );
        // @todo check for the HTTP status code is `204` rather than $response being `null`
        if ( is_null( $response ) ) {
            $this->info( "Successfully deleted group membership" );
        } else {
            $this->error( 'Unexpected response' );
            $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );
        }

        return self::SUCCESS;
    }
}
