<?php

namespace App\Commands;

use Illuminate\Support\Facades\Artisan;
use Mockery\Exception;

class TriggersDeleteCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'triggers:delete
                            {id* : IDs of the triggers to delete. Multiple IDs separated by a space}
                            {--dry-run : Output the details of the trigger deletion without executing the deletion}
                            {--yes : Bypass confirmation step and delete triggers}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete triggers';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {
        $params = array( 'id' );

        if ( $this->option( 'dry-run' ) ) {
            $this->handle_dry_run( $this->argument( 'id' ) );
        }

        if ( $this->confirm( 'Do you wish to continue?' ) ) {

            try {

            } catch ( Exception $exception ) {
                $this->error( $exception->getMessage() );

                return self::FAILURE;
            }

        }

        return self::SUCCESS;
    }

    private function handle_dry_run( array $ids ) {
        $triggers = array();

        foreach ( $ids as $id ) {
            $triggers[] = Artisan::call( 'triggers', [ 'trigger-id' => $id ] );
        }

        $this->info( count( $triggers ) . " triggers would be deleted." );
        $rows = array();
        foreach ( $triggers as $trigger ) {
            $rows[] = array( $trigger->id, $trigger->name );
        }
        $this->table( [ 'ID', 'Name' ], $rows );
    }

}
