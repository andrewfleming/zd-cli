<?php

namespace App\Commands;

class TagsSearchCommand extends ZendeskBaseCommand {
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tags:search
                            {query : A substring of a tag to search for}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Returns an array of registered and recent tag names that start with the characters specified in the name query parameter.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        if ( strlen( $this->argument( 'query' ) ) <= 2 ) {
            $this->error( 'The query must be two or more characters' );

            return self::FAILURE;
        }

        $query_params = array( 'tag_name_fragment' => $this->argument( 'query' ) );

        try {
            $response = $this->Zendesk->autocomplete()->tags( $query_params );
        } catch ( \Exception $exception ) {
            $this->line( $exception->getMessage() );

            return self::FAILURE;
        }

        if ( is_null( $response ) ) {
            var_dump(print_r($this->Zendesk->getDebug(), true));
        }
        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );

        return self::SUCCESS;
    }
}
