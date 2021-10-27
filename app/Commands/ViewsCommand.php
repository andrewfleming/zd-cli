<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;

class ViewsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'views
                            {view-id? : ID of the Organization you want to retrieve}
                            {--group-id= : Only views belonging to given group}
                            {--access= : Only views with given access. May be "personal", "shared", or "account"}
                            {--active= : Only active views if true, inactive views if false}
                            {--max : Return all pages of search results up to the 10 pages max (1000 results)}
                            {--sort-by= : One of "alphabetical", "created_at", or "updated_at". Defaults to "position"}
                            {--sort-order= : One of "asc" or "desc". Defaults to "asc" for alphabetical and position sort, "desc" for all others}
                            {--include= : Side-load related records as part of a single request. Pass a comma-separated list of available records; app_installation, permissions}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Lists shared and personal views available to the current user.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {
        $query_params = array(
            'sort_by'    => $this->option( 'sort-by' ),
            'sort_order' => $this->option( 'sort-order' ),
            'page'       => 1,
            'group_id'   => $this->option( 'group-id' ),
            'active'     => $this->option( 'active' ),
            'access'     => $this->option( 'access' ),
        );
        $view_objects = array();

        if ( $this->option( 'include' ) ) {
            $query_params['include'] = explode( ',', $this->option( 'include' ) );
        }

        try {

            if ( $this->argument( 'view-id' ) ) {
                $response = $this->Zendesk->views()->find( $this->argument( 'view-id' ) );
            } elseif ( $this->option( 'max' ) ) {

                do {
                    $response     = $this->Zendesk->views()->findAll( $query_params );
                    $view_objects = array_merge( $view_objects, $response->views );
                    $query_params['page'] ++;
                } while ( $response->next_page && $this->option( 'max' ) && $query_params['page'] < 11 );

                $response->views = $view_objects;
            } else {
                $response = $this->Zendesk->views()->findAll( $query_params );
            }

        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );

            return Command::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

        return Command::SUCCESS;
    }

}
