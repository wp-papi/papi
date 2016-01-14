<?php

class Term_Entry1_Type extends Papi_Entry_Type {

	/**
	 * Define our Entry Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Term test entry 1',
			'description' => 'Entry type to test out the term property',
			'id'          => 'term-entry-type'
		];
	}
}
