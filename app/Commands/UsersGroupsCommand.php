<?php

namespace App\Commands;

class UsersGroupsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'users:groups
                            {id : User ID}
                            {--members : Show the group relationship data rather than group data}
                            {--assignable : List of groups the user can be assigned to}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get the groups a user is member of ';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        try {

            if ( $this->option( 'assignable' ) ) {
                $response = $this->Zendesk->users( $this->argument( 'id' ) )->groups()->assignable();
            } else {
                $response = $this->Zendesk->users( $this->argument( 'id' ) )->groups()->findAll();
            }

        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );

            return self::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

        return self::SUCCESS;
    }

}
