<?php

/**
 * HTML hidden input property.
 */
class Papi_Property_Hidden extends Papi_Property_String {

	/**
	 * Don't display the property in WordPress admin.
	 *
	 * @var bool
	 */
	protected $display = false;

	/**
	 * The input type to use.
	 *
	 * @var string
	 */
	public $input_type = 'hidden';

}
