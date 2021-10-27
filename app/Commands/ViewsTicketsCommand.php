<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\RouteException;

class ViewsTicketsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'views:tickets
                            {view-id : View ID}
                            {--all : Return all pages of search results up to the 10 pages max (1000 results)}
                            {--sort-order= : One of "asc" or "desc". Defaults to "asc" for alphabetical and position sort, "desc" for all others}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List tickets from a view';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {
        $query_params   = array(
            'id'         => $this->argument( 'view-id' ),
            'sort_order' => $this->option( 'sort-order' ),
            'page'       => 1,
        );
        $ticket_objects = array();

        do {

            try {
                $request = $this->Zendesk->views()->tickets( $query_params );
            } catch ( RouteException | MissingParametersException $exception ) {
                $this->error( $exception->getMessage() );

                return Command::FAILURE;
            }

            $ticket_objects = array_merge( $ticket_objects, $request->tickets );
            $query_params['page'] ++;
        } while ( $request->next_page && $this->option( 'all' ) );

        $request->tickets = $ticket_objects;
        $this->line( json_encode( $request, JSON_PRETTY_PRINT ) );

        return Command::SUCCESS;
    }

}
