<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;

class AuditLogsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'audit-logs
                            {audit-log-id? : The ID of the audit log}
                            {--actor-id= : Filter audit logs by the actor id}
                            {--created-at= : Filter audit logs by the time of creation}
                            {--ip-address= : Filter audit logs by the ip address}
                            {--source-type= : Filter audit logs by the source type. For example, user or rule}
                            {--source-id= : Filter audit logs by the source id. Requires filter[source_type] to also be set.}
                            {--sort-by= : Defaults to "created_at"}
                            {--sort-order= : One of "asc" or "desc". Defaults to "desc"}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'The audit log shows various changes in your Zendesk since the account was created';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $query_params = array_filter( array(
            'sort_by'             => $this->option( 'sort-by' ),
            'sort_order'          => $this->option( 'sort-order' ),
            'page'                => 1,
            'filter[actor_id]'    => $this->option( 'actor-id' ),
            //            'filter[created_at][]'  => $this->option( 'created-at' ),
            //            'filter[created_at][]'  => "2021-05-22T20:10:34Z",
            'filter[ip_address]'  => $this->option( 'ip-address' ),
            'filter[source_type]' => $this->option( 'source-type' ),
            'filter[source_id]'   => $this->option( 'source-id' ),
        ) );

        try {

            if ( $this->argument( 'audit-log-id' ) ) {
                $response = $this->Zendesk->auditLogs()->find( $this->argument( 'audit-log-id' ) );
            } else {
                $response = $this->Zendesk->auditLogs()->findAll( $query_params );
            }

        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );

            return Command::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

        return Command::SUCCESS;
    }

}
