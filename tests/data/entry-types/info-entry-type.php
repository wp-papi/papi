<?php

class Info_Entry_Type extends Papi_Entry_Type {

	/**
	 * Define our entry type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'                => 'Info entry type',
			'description'         => 'This is a Info entry type',
			'sort_order'          => 500,
			'show_screen_options' => false
		];
	}

	/**
	 * Add custom body classes.
	 *
	 * @return array
	 */
	public function body_classes() {
		return ['info-entry-type'];
	}

	/**
	 * Add help tabs.
	 *
	 * @return array
	 */
	public function help() {
		return [
			'Hello 1' => 'Lorem ipsum',
			'Hello 2' => function () {
				return 'Lorem ipsum 2';
			}
		];
	}

	/**
	 * Define our properties.
	 */
	public function register() {
		$this->box( 'Info', papi_property( [
			'type'  => 'string',
			'title' => 'Info'
		] ) );

		$this->box( ['title' => 'Site 2', 'site_id' => 2], papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] ) );
	}
}
