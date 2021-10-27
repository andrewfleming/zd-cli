<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use App\Files\Config;

class ConfigCommand extends Command {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'config';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Define configuration settings from your Zendesk account';

    /**
     * Execute the console command.
     *
     * @param Config $config Class interacting with config file
     *
     * @return mixed
     */
    public function handle( Config $config ) {
        $subdomain     = $this->ask( 'Zendesk subdomain' );
        $client_id     = $this->secret( 'Zendesk OAuth client ID' );
        $client_secret = $this->secret( 'Zendesk OAuth client secret' );

        if ( ! isset( $subdomain, $client_id, $client_secret ) ) {
            $this->error( 'Required options missing. Use --help option to see details.' );

            return $this::FAILURE;
        }

        $config->write_file( compact( 'subdomain', 'client_id', 'client_secret' ) );
        $this->info( 'Config saved successfully' );

        return $this::SUCCESS;
    }
}
