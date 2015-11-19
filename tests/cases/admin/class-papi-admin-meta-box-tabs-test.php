<?php

/**
 * @group admin
 */
class Papi_Admin_Meta_Box_Tabs_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->tabs = [
			papi_tab( [ 'title' => 'Content' ] ),
			papi_tab( [ 'title' => 'More', 'sort_order' => 1 ], [
				papi_property( [
					'type'  => 'string',
					'title' => 'Name'
				] )
			] )
		];
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->tabs );
	}

	public function test_construct() {
		$class = new Papi_Admin_Meta_Box_Tabs();
		$this->assertEmpty( $class->get_tabs() );
	}

	public function test_papi_admin_meta_box_tab_class() {
		$class = new Papi_Admin_Meta_Box_Tabs( $this->tabs, false );
		$tabs  = $class->get_tabs();

		$this->assertSame( 'More', $tabs[0]->title );
		$this->assertSame( 1, $tabs[0]->sort_order );
		$this->assertSame( 'string', $tabs[0]->properties[0]->type );
		$this->assertSame( 'Name', $tabs[0]->properties[0]->title );
		$this->assertSame( 'Content', $tabs[1]->title );
		$this->assertSame( 1000, $tabs[1]->sort_order );
		$this->assertEmpty( $tabs[1]->properties );
	}

	public function test_render() {
		papi_render_properties( $this->tabs );
		$this->expectOutputRegex( '/.*\S.*/' );
	}

	public function test_tabs() {
		$this->assertSame( 'Content', $this->tabs[0]->title );
		$this->assertEmpty( $this->tabs[0]->properties );
		$this->assertSame( 'More', $this->tabs[1]->title );
		$this->assertSame( 'string', $this->tabs[1]->properties[0]->type );
		$this->assertSame( 'Name', $this->tabs[1]->properties[0]->title );
	}
}
