<?php

namespace App\Commands;

use Exception;

class AutomationsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'automations
                            {--side-load= : Retrieve related records as part of a single request. Pass a comma-separated list of available records; app_installation, permissions, usage_1h, usage_24h, usage_7d, usage_30d}
                            {--active=true : Toggle for requesting active or inactive automations}
                            {--sort-by=position : Possible values are "alphabetical", "created_at", "updated_at", "usage_1h", "usage_24h", or "usage_7d". Defaults to "position"}
                            {--sort-order=desc : "One of "asc" or "desc". Defaults to "asc" for alphabetical and position sort, "desc" for all others"}
                            {--max : Request multiple pages automations, up to a maximum 1000}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List automations from your account.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {
        $automations = array();
        $params      = array(
            'page'       => 1,
            'active'     => $this->option( 'active' ),
            'sort_by'    => $this->option( 'sort-by' ),
            'sort_order' => $this->option( 'sort-order' ),
        );

        if ( $this->option( 'side-load' ) ) {
            $params['include'] = explode( ',', $this->option( 'side-load' ) );
        }

        try {
            do {
                $response    = $this->Zendesk->automations()->findAll( $params );
                $automations = array_merge( $automations, $response->automations );
                $params['page'] ++;
            } while ( $response->next_page && $this->option( 'max' ) && $params['page'] < 11 );

            $response->automations = $automations;
            $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

            return self::SUCCESS;
        } catch ( Exception $exception ) {
                $this->error( $exception->getMessage() );

            return self::FAILURE;
        }
    }

}
