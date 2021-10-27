<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Zendesk\API\Exceptions\MissingParametersException;

class OrganizationsTagsCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'organizations:tags
                            {org-id : Organization ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List tags used on an organizations tickets.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $query_params = array();
        $request = null;

        try {
            $request = $this->Zendesk->organizations( [ 'id' => $this->argument('org-id' ) ] )->tags()->find();
        } catch ( MissingParametersException $exception ) {
            $this->error( $exception->getMessage() );
        }

        $this->line( json_encode( $request, JSON_PRETTY_PRINT ) );
    }

}
