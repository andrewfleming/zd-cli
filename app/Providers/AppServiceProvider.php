<?php

namespace App\Providers;

use App\Commands\AuthCommand;
use App\Files\Config;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Client\Provider\AbstractProvider;
use Stevenmaguire\OAuth2\Client\Provider\Zendesk as OAuthProvider;
use Zendesk\API\HttpClient;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        $this->app->bind( 'Illuminate\Encryption', function( $app ) {
            return new Encrypter( config( 'app.key' ), 'AES-256-CBC' );
        });
        $this->app->singleton( Config::class, function( $app ) {
            return new Config( $app->make( 'Illuminate\Encryption' ) );
        } );

        $this->app->singleton( AbstractProvider::class, function( $app ) {
            $config = $app->make( 'App\Files\Config' );
            $vars = $config->read_file();
            return new OAuthProvider( array(
                'clientId'     => $vars['client_id'],
                'clientSecret' => $vars['client_secret'],
                'redirectUri'  => 'http://' . AuthCommand::IP . ':' . AuthCommand::PORT . '/authorization-code/callback',
                'subdomain'    => $vars['subdomain'],
            ) );
        } );

        $this->app->singleton( HttpClient::class, function( $app ) {
            $config = $app->make( 'App\Files\Config' );
            $vars = $config->read_file();
            return new HttpClient( $vars['subdomain'] );
        });

    }
}
