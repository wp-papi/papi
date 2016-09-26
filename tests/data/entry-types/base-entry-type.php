<?php

abstract class Base_Entry_Type extends Papi_Entry_Type {
	abstract public function meta();
	abstract public function register();

	public function register_content_box() {
		$this->box( 'Content', [
			papi_property( [
				'title' => 'Name',
				'type'  => 'string'
			] )
		] );
	}
}
