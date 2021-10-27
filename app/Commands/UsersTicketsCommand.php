<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class UsersTicketsCommand extends ZendeskBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'users:tickets
                            {id : User ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List a users tickets';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $params = array();

        try{
            $response = $this->Zendesk->users( $this->argument( 'id' ) )->tickets()->assigned();
        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );
            return $this::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );
        return $this::SUCCESS;
    }

}
