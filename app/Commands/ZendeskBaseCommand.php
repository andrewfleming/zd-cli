<?php

namespace App\Commands;

use App\Files\Config;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Zendesk\API\Exceptions\AuthException;
use Zendesk\API\HttpClient;

abstract class ZendeskBaseCommand extends Command {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'base';

    protected $Zendesk;

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '';

    public function __construct( HttpClient $Zendesk, Config $config ) {
        parent::__construct();
        $this->Zendesk = $Zendesk;
        $vars          = $config->read_file();

        if ( array_key_exists( 'access_token', $vars ) ) {

            try {
                $this->Zendesk->setAuth( 'oauth', array( 'token' => $vars['access_token'] ) );
            } catch ( AuthException $exception ) {
                echo "Unable to access or authenticate with auth token. Run zd config and zd auth";
            }
        }

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        //
    }

    /**
     * Define the command's schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule( Schedule $schedule ): void {
        // $schedule->command(static::class)->everyMinute();
    }
}
