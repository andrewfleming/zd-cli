<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class UsersCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'users
                            {ids?* : Zendesk user ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve data for a Zendesk user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        try {

            if ( $this->argument( 'ids' ) ) {
                $response = $this->Zendesk->users()->findMany( array( 'ids' => $this->argument( 'ids' ) ) );
            } else {
                $response = $this->Zendesk->users()->findAll();
            }

        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );

            return self::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

        return self::SUCCESS;
    }
}
