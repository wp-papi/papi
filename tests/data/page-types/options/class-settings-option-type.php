<?php

class Settings_Option_Type extends Papi_Option_Type {

	public function meta() {
		return [
			'name' => 'Settings',
			'menu' => 'options-general.php'
		];
	}

	public function register() {
		$this->box( 'Settings', [
			papi_property( [
				'title' => 'Link',
				'type' => 'link'
			] )
		] );
	}
}
