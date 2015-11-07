<?php

class Media_Attachment_Type extends Papi_Attachment_Type {

	public function page_type() {
		return [
			'name' => 'Media'
		];
	}

	public function register() {
		$this->box( 'Content', [
			papi_property( [
				'title' => 'Media name',
				'slug'  => 'media_name',
				'type'  => 'string'
			] ),
			papi_property( [
				'title' => 'Media post',
				'slug'  => 'media_post',
				'type'  => 'post'
			] ),
			papi_property( [
				'title' => 'Media text',
				'slug'  => 'media_text',
				'type'  => 'text'
			] )
		] );
	}
}
