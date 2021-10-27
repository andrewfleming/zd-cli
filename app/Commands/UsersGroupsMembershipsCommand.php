<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;

class UsersGroupsMembershipsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'users:groups:memberships
                            {id : The user ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show the group membership data for a user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        try {
            $response = $this->Zendesk->users( $this->argument( 'id' ) )->groupMemberships()->findAll();
        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );
            return self::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT) );
        return self::SUCCESS;
    }

}
