<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Zendesk\API\Exceptions\ApiResponseException;
use Zendesk\API\Exceptions\MissingParametersException;
use \Zendesk\API\Exceptions\RouteException;

class SearchCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'search
                            {query-string : The query to search for. Result is returned as JSON.}
                            {--sort-by= : One of updated_at, created_at, priority, status, or ticket_type. Defaults to sorting by relevance}
                            {--sort-order= : One of asc or desc. Defaults to desc}
                            {--all : Return all pages of search results up to the 10 pages (1000 results)}';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Search tickets, users, organisations';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {
        $query        = $this->argument( 'query-string' );
        $query_params = array(
            'sort_by'    => $this->option( 'sort-by' ),
            'sort_order' => $this->option( 'sort-order' ),
            'page'       => 1,
        );
        $results      = array();

        do {

            try {
                $request = $this->Zendesk->search()->find( $query, $query_params );
            } catch ( RouteException | MissingParametersException | ApiResponseException $exception ) {
                $this->error( $exception->getMessage() );

                return Command::FAILURE;
            }

            $results = array_merge( $results, $request->results );
            $query_params['page'] ++;
        } while ( $request->next_page && $this->option( 'all' ) && $query_params['page'] < 11 );

        $request->results = $results;
        $this->line( json_encode( $request, JSON_PRETTY_PRINT ) );

        return Command::SUCCESS;
    }

}
