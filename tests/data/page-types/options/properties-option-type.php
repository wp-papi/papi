<?php

class Properties_Option_Type extends Papi_Option_Type {

	public function meta() {
		return [
			'name' => 'Properties',
			'menu' => 'options-general.php'
		];
	}

	public function register() {
		$this->box( 'boxes/properties.php' );
	}

}
