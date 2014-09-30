<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi - Property Hidden
 *
 * @package Papi
 * @version 1.0.0
 */
class PropertyHidden extends PropertyString {

	/**
	 * The input type to use.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $input_type = 'hidden';

}
