<?php

namespace App\Commands;

class UsersGroupsMembershipsCreateCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'users:groups:memberships:create
                            {user-id : User ID}
                            {group-id : Group ID}
                            {--make-default : Make this group the users default}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add a user to a group';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $params = array(
            'user_id'  => $this->argument( 'user-id' ),
            'group_id' => $this->argument( 'group-id' )
        );

        try {
            $response = $this->Zendesk->users( $this->argument( 'user-id' ) )->groups()->memberships()->create( $params );
            $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

            // @todo Split this out so it's possible to make a relationship default independent from creating a group as well. Rerunning the create
            // command for an existing group membership results in `422 Unprocessable Entity` responses and throws an exception
            if ( $this->option( 'make-default' ) ) {

                if ( isset( $response->group_membership->id ) ) {
                    $response = $this->Zendesk->users( $this->argument( 'user-id' ) )->groups()->memberships()->makeDefault( array(
                        'id'     => $response->group_membership->id,
                        'userId' => $this->argument( 'user-id' )
                    ) );
                    $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );
                } else {
                    $this->error( 'Failed to make default group membership' );
                }

            }

        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

}
