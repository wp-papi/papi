<?php

/**
 * Manage Papi from CLI.
 */
class Papi_CLI extends WP_CLI_Command {
}

WP_CLI::add_command( 'papi', 'Papi_CLI' );
WP_CLI::add_command( 'papi post', 'Papi_CLI_Post_Command' );
WP_CLI::add_command( 'papi term', 'Papi_CLI_Term_Command' );
WP_CLI::add_command( 'papi type', 'Papi_CLI_Type_Command' );
