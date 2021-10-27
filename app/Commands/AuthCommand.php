<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use App\{Server, Files};

class AuthCommand extends Command {
    const IP = '127.0.0.1';
    const PORT = '8090';
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'auth';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Authorize ZD-CLI to access your Zendesk account using OAuth 2';

    /**
     * Execute the console command.
     *
     * @param AbstractProvider $provider OAuth 2.0 service provider for Zendesk
     * @param Files\Config $config Class for interacting with config file
     * @return integer Exit code
     */
    public function handle( AbstractProvider $provider, Files\Config $config ) {
        $this->line( 'Open this URL in a browser to authorize ZD-CLI' );
        $this->line( $provider->getAuthorizationUrl( array( 'scope' => 'read write' ) ) );
        $response = $this->handle_oauth_response();

        if ( ! is_array( $response ) || ! array_key_exists( 'code', $response ) ) {
            $this->error( 'Failed to retrieve authorization code' );
            return Command::FAILURE;
        }

        $access_token = $this->request_access_token( $provider, $response['code'] );

        if ( $access_token instanceof IdentityProviderException ) {
            $this->error( "Failed to obtain access token: { $access_token->getMessage() }" );
            return Command::FAILURE;
        }

        if ( ! $this->store_access_token( $access_token, $config ) ) {
            $this->error('Failed to store access token' );
            return Command::FAILURE;
        }

        $this->info( 'Authorization successful' );
        return Command::SUCCESS;
    }

    private function store_access_token( string $access_token, Files\Config $config ) {
        $vars = $config->read_file();
        $vars['access_token'] = $access_token;
        return $config->write_file( $vars );
    }

    private function request_access_token( AbstractProvider $provider, $auth_code ) {
        try {
            return $provider->getAccessToken( 'authorization_code', array( 'code' => $auth_code ) );
        } catch ( IdentityProviderException $e ) {
            return $e;
        }
    }

    private function handle_oauth_response() {
        $server = new Server\HTTP();
        $stream = $server->server( $this->socket_string() );
        return $server->accept( $stream );
    }

    private function socket_string() {
        return 'tcp://' . self::IP . ':' . self::PORT;
    }

}
