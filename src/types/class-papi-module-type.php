<?php

/**
 * Papi type that handle all post types except attachment.
 * All page types should extend this class.
 */
class Papi_Module_Type extends Papi_Page_Type {

	/**
	 * The post types to register the page type with.
	 *
	 * @var array
	 */
	public $post_type = ['module'];
}
