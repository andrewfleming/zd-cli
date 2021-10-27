<?php

namespace App\Commands;

use App\Files\Config;
use Zendesk\API\HttpClient;

class TicketsUpdateBulkCommand extends ZendeskBaseCommand {
    protected $ticket_properties = array(
        'assignee_id'      => array(
            'filter'      => FILTER_VALIDATE_INT,
            'options'     => array( 'min_range' => 0 ),
            'description' => 'The agent currently assigned to the ticket'
        ),
        'brand_id'         => array(
            'filter'      => FILTER_VALIDATE_INT,
            'options'     => array( 'min_range' => 0 ),
            'description' => 'Enterprise only. The id of the brand this ticket is associated with'
        ),
        'collaborator_ids' => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'valid'       => 'is_numeric',
            'description' => 'The ids of users currently CC\'ed on the ticket',
        ),
        'comment'          => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'description' => 'A comment to add to a ticket.',
        ),
        'comment_author'   => array(
            'filter'      => FILTER_VALIDATE_INT,
            'options'     => array( 'min_range' => 0 ),
            'description' => 'The Zendesk user ID for the agent the comment is from.',
        ),
        //        'custom_fields'    => array(
        //            'type'        => 'array',
        //            'description' => 'Custom fields for the ticket. See Setting custom field values'
        //        ),
        'due_at'           => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'description' => 'If this is a ticket of type "task" it has a due date. Due date format uses ISO 8601 format.'
        ),
        'email_cc_ids'     => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'valid'       => 'is_numeric',
            'description' => 'The ids of agents or end users currently CC\'ed on the ticket. See CCs and followers resources in the Support Help Center'
        ),
        'external_id'      => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'valid'       => 'is_numeric',
            'description' => 'An id you can use to link Zendesk Support tickets to local records'
        ),
        'follower_ids'     => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'valid'       => 'is_numeric',
            'description' => 'The ids of agents currently following the ticket. See CCs and followers resources'
        ),
        'group_id'         => array(
            'filter'      => FILTER_VALIDATE_INT,
            'options'     => array( 'min_range' => 0 ),
            'description' => 'The group this ticket is assigned to'
        ),
        'macro_ids'        => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'valid'       => 'is_numeric',
            'description' => 'List of macro IDs to be recorded in the ticket audit'
        ),
        'organization_id'  => array(
            'filter'      => FILTER_VALIDATE_INT,
            'options'     => array( 'min_range' => 0 ),
            'description' => 'The organization of the requester. You can only specify the ID of an organization associated with the requester. See Organization Memberships'
        ),
        'priority'         => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'description' => 'The urgency with which the ticket should be addressed. Allowed values are "urgent", "high", "normal", or "low".',
            'valid'       => array( "urgent", "high", "normal", "low" )
        ),
        'problem_id'       => array(
            'filter'      => FILTER_VALIDATE_INT,
            'options'     => array( 'min_range' => 0 ),
            'description' => 'For tickets of type "incident", the ID of the problem the incident is linked to'
        ),
        'raw_subject'      => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'description' => 'The dynamic content placeholder, if present, or the "subject" value, if not. See Dynamic Content'
        ),
        'recipient'        => array(
            'filter'      => FILTER_SANITIZE_EMAIL,
            'description' => 'The original recipient e-mail address of the ticket'
        ),
        'requester_id'     => array(
            'filter'      => FILTER_VALIDATE_INT,
            'options'     => array( 'min_range' => 0 ),
            'description' => 'The user who requested this ticket'
        ),
        'status'           => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'valid'       => array( "new", "open", "pending", "hold", "solved", "closed" ),
            'description' => 'The state of the ticket. Allowed values are "new", "open", "pending", "hold", "solved", or "closed".'
        ),
        'subject'          => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'description' => 'The value of the subject field for this ticket'
        ),
        'submitter_id'     => array(
            'filter'      => FILTER_VALIDATE_INT,
            'options'     => array( 'min_range' => 0 ),
            'description' => 'The user who submitted the ticket. The submitter always becomes the author of the first comment on the ticket'
        ),
        'tags'             => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'description' => 'The array of tags applied to this ticket'
        ),
        'ticket_form_id'   => array(
            'filter'      => FILTER_VALIDATE_INT,
            'options'     => array( 'min_range' => 0 ),
            'description' => 'Enterprise only. The id of the ticket form to render for the ticket'
        ),
        'type'             => array(
            'filter'      => FILTER_SANITIZE_STRING,
            'description' => 'The type of this ticket. Allowed values are "problem", "incident", "question", or "task".',
            'valid'       => array( "problem", "incident", "question", "task" )
        )
    );

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = ''; // overwritten in `set_signature`

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Make the same update to multiple tickets.';

    public function __construct( HttpClient $Zendesk, Config $config ) {
        $this->set_signature();
        parent::__construct( $Zendesk, $config );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {
        $params = array_filter( filter_var_array( $this->options(), $this->ticket_properties, false ) );

        foreach ( array_keys( $params ) as $key ) {
            $properties = $this->ticket_properties[ $key ];

            if ( ! $this->is_valid_option( $key ) ) {
                $valid_msg = is_array( $properties['valid'] ) ? "are: " . implode( ", ", $properties['valid'] ) : $properties['valid'];
                $this->error( "--$key contain invalid data. Allowed values " . $valid_msg );

                return self::FAILURE;
            }

        }

        $params['ids'] = $this->argument( 'ids' );

        if ( $this->option( 'dry-run' ) ) {
            $this->line( 'Dry run: Tickets '. implode( ', ', $this->argument( 'ids' ) ) . ' will be updated with:' . PHP_EOL . json_encode( $params, JSON_PRETTY_PRINT ) );
            return self::SUCCESS;
        }

        try {
            $response = $this->Zendesk->tickets()->updateMany( $params );
        } catch ( \Exception $exception ) {
            $this->error( $exception->getMessage() );
            return self::FAILURE;
        }

        $this->line( json_encode( $response, JSON_PRETTY_PRINT ) );
        return self::SUCCESS;
    }

    private function sanitize_ticket_ids( array $ids ) {}
    private function valid_ticket_ids( array $ids ) {}

    private function is_valid_option( string $key ): bool {
        $properties = $this->ticket_properties[ $key ];

        if ( array_key_exists( 'valid', $properties ) ) {

            if ( is_array( $properties['valid'] ) ) {
                return in_array( strtolower( $this->option( $key ) ), $properties['valid'] );
            } elseif ( is_callable( $properties['valid'] ) ) {
                $sep_values = explode( ',', $this->option( $key ) );
                $validated  = array_map( $properties['valid'], $sep_values );

                /**
                 * array_search returning false means all values were valid. We match false exactly because array_search
                 * returns 0 if the first array element is false.
                 */
                return ( false === array_search( false, $validated, true ) );
            } else {
                return false;
            }

        }

        return true; // No options with a list of valid options
    }

    private function set_signature() {
        $options = array_map(
            array( $this, 'format_signature_options' ),
            array_keys( $this->ticket_properties ),
            array_values( $this->ticket_properties )
        );
        $this->signature = sprintf(
            "tickets:update:many
                    {ids?* : Space seperated list of ticket IDs}
                    {--dry-run : Output details about the update but do not execute the update.}
                    %s",
            implode( PHP_EOL, $options )
        );
    }

    private function format_signature_options( string $key, array $values ): string {
        return sprintf( "{--%s= : %s}", $key, $values['description'] );
    }

}
