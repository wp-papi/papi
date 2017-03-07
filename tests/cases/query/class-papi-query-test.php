<?php

/**
 * @group query
 */
class Papi_Query_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_empty_query() {
		$query = new Papi_Query();

		$this->assertEmpty( $query->get_result() );
	}

	public function test_missing_query() {
		$query = new Papi_Query( [
			'entry_type' => 'fake-page-type',
			'fields'     => 'ids'
		] );

		$this->assertEmpty( $query->get_result() );

		$post_id = $this->factory->post->create( ['post_type' => 'page'] );
		update_post_meta( $post_id, papi_get_page_type_key(), 'fake-page-type' );

		$this->assertEmpty( $query->get_result() );
	}

	public function test_first() {
		$query = new Papi_Query( [
			'entry_type' => 'simple-page-type',
			'fields'     => 'ids'
		] );

		$this->assertEmpty( $query->first() );

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/page-types';
		} );

		$post_id1 = $this->factory->post->create( ['post_type' => 'page'] );
		update_post_meta( $post_id1, papi_get_page_type_key(), 'simple-page-typee' );

		$post_id2 = $this->factory->post->create( ['post_type' => 'page'] );
		update_post_meta( $post_id2, papi_get_page_type_key(), 'simple-page-type' );

		$result = $query->get_result();
		$first  = array_shift( $result );

		$this->assertSame( $first, $query->first() );
	}

	public function test_last() {
		$query = new Papi_Query( [
			'entry_type' => 'simple-page-type',
			'fields'     => 'ids'
		] );

		$this->assertEmpty( $query->last() );

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/page-types';
		} );

		$post_id1 = $this->factory->post->create( ['post_type' => 'page'] );
		update_post_meta( $post_id1, papi_get_page_type_key(), 'simple-page-type' );

		$post_id2 = $this->factory->post->create( ['post_type' => 'page'] );
		update_post_meta( $post_id2, papi_get_page_type_key(), 'simple-page-type' );

		$result = $query->get_result();
		$last   = array_pop( $result );

		$this->assertSame( $last, $query->last() );
	}

	public function test_simple_query() {
		$query = new Papi_Query( [
			'entry_type' => 'simple-page-type',
			'fields'     => 'ids'
		] );


		$this->assertEmpty( $query->get_result() );

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/page-types';
		} );

		$post_id = $this->factory->post->create( ['post_type' => 'page'] );
		update_post_meta( $post_id, papi_get_page_type_key(), 'simple-page-type' );

		$this->assertSame( [$post_id], $query->get_result() );
	}

	public function test_real_page_type_query_fail() {
		$query = new Papi_Query( [
			'fields'    => 'ids',
			'page_type' => 'simple-page-type'
		] );

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/page-types';
		} );

		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, papi_get_page_type_key(), 'simple-page-type' );

		// Wrong post type.
		$this->assertEmpty( $query->posts );
	}

	public function test_real_page_type_query_success() {
		$query = new Papi_Query( [
			'fields'    => 'ids',
			'page_type' => 'simple-page-type'
		] );

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/page-types';
		} );

		$post_id = $this->factory->post->create( ['post_type' => 'page'] );
		update_post_meta( $post_id, papi_get_page_type_key(), 'simple-page-type' );

		$this->assertSame( [$post_id], $query->posts );
	}

	public function test_real_page_type_meta_query() {
		$query = new Papi_Query( [
			'fields'     => 'ids',
			'page_type'  => 'simple-page-type',
			'meta_query' => [
				[
					'key'   => 'name',
					'value' => 'Fredrik'
				]
			]
		] );

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/page-types';
		} );

		$post_id = $this->factory->post->create( ['post_type' => 'page'] );
		update_post_meta( $post_id, papi_get_page_type_key(), 'simple-page-type' );

		$this->assertEmpty( $query->posts );

		update_post_meta( $post_id, 'name', 'Fredrik' );

		$this->assertSame( [$post_id], $query->posts );
	}

	public function test_real_page_type_meta_key_value() {
		$query = new Papi_Query( [
			'fields'       => 'ids',
			'page_type'    => 'simple-page-type',
			'meta_key'     => 'name',
			'meta_value'   => 'Fredrik',
		    'meta_compare' => '='
		] );

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/page-types';
		} );

		$post_id = $this->factory->post->create( ['post_type' => 'page'] );
		update_post_meta( $post_id, papi_get_page_type_key(), 'simple-page-type' );

		$this->assertEmpty( $query->posts );

		update_post_meta( $post_id, 'name', 'Fredrik' );

		$this->assertSame( [$post_id], $query->posts );
	}

	public function test_real_taxonomy_type_query_fail() {
		if ( ! papi_supports_term_meta() ) {
			$this->markTestSkipped( 'Term metadata is not supported' );
		}

		if ( ! class_exists( 'WP_Term_Query' ) ) {
			$this->markTestSkipped( 'Term query is not supported' );
		}

		$query = new Papi_Query( [
			'fields'        => 'ids',
			'taxonomy_type' => 'simple-taxonomy-type'
		], 'term' );

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/taxonomy-types';
		} );

		$term_id = $this->factory->term->create( ['taxonomy' => 'fake'] );
		update_term_meta( $term_id, papi_get_page_type_key(), 'simple-taxonomy-type' );

		// Wrong taxonomy.
		$this->assertEmpty( $query->terms );
	}

	public function test_real_taxonomy_type_query_success() {
		if ( ! papi_supports_term_meta() ) {
			$this->markTestSkipped( 'Term metadata is not supported' );
		}

		if ( ! class_exists( 'WP_Term_Query' ) ) {
			$this->markTestSkipped( 'Term query is not supported' );
		}

		$query = new Papi_Query( [
			'fields'        => 'ids',
			'hide_empty'    => false,
			'taxonomy_type' => 'simple-taxonomy-type'
		], 'term' );

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/taxonomy-types';
		} );

		$term_id = $this->factory->term->create( ['taxonomy' => 'category'] );
		update_term_meta( $term_id, papi_get_page_type_key(), 'simple-taxonomy-type' );

		$this->assertEquals( [$term_id], $query->terms );
	}

	public function test_real_taxonomy_type_meta_query() {
		if ( ! papi_supports_term_meta() ) {
			$this->markTestSkipped( 'Term metadata is not supported' );
		}

		if ( ! class_exists( 'WP_Term_Query' ) ) {
			$this->markTestSkipped( 'Term query is not supported' );
		}

		$query = new Papi_Query( [
			'fields'     => 'ids',
			'entry_type' => 'simple-taxonomy-type',
			'hide_empty' => false,
			'meta_query' => [
				[
					'key'   => 'name',
					'value' => 'Fredrik'
				]
			]
		], 'term' );

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/taxonomy-types';
		} );

		$term_id = $this->factory->term->create( ['taxonomy' => 'category'] );
		update_term_meta( $term_id, papi_get_page_type_key(), 'simple-taxonomy-type' );

		$this->assertEmpty( $query->terms );

		update_term_meta( $term_id, 'name', 'Fredrik' );

		$this->assertEquals( [$term_id], $query->terms );
	}

	public function test_real_taxonomy_type_meta_key_value() {
		if ( ! papi_supports_term_meta() ) {
			$this->markTestSkipped( 'Term metadata is not supported' );
		}

		if ( ! class_exists( 'WP_Term_Query' ) ) {
			$this->markTestSkipped( 'Term query is not supported' );
		}

		$query = new Papi_Query( [
			'fields'     => 'ids',
			'entry_type' => 'simple-taxonomy-type',
			'meta_key'   => 'name',
			'meta_value' => 'Fredrik',
			'hide_empty' => false
		], 'term' );

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/taxonomy-types';
		} );

		$term_id = $this->factory->term->create( ['taxonomy' => 'category'] );
		update_term_meta( $term_id, papi_get_page_type_key(), 'simple-taxonomy-type' );

		$this->assertEmpty( $query->terms );

		update_term_meta( $term_id, 'name', 'Fredrik' );

		$this->assertEquals( [$term_id], $query->terms );
	}
}
