<?php

class Attachment_Type extends Papi_Attachment_Type {

	public function page_type() {
		return [
			'name' => 'Attachment'
		];
	}

	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'title' => 'Name',
				'slug'  => 'name',
				'type'  => 'string'
			] ),
			papi_property( [
				'title' => 'Post',
				'slug'  => 'post',
				'type'  => 'post'
			] ),
			papi_property( [
				'title' => 'Text',
				'slug'  => 'text',
				'type'  => 'text'
			] )
		] );
	}
}
