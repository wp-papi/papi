<?php

/**
 * @group properties
 */
class Papi_Property_Module_Test extends Papi_Property_Test_Case {

	public $slug = 'module_test';

	public function assert_values( $expected, $actual, $slug ) {
		$this->assertSame( $expected->module->ID, $actual->module->ID );
		$this->assertSame( $expected->template, $actual->template );
	}

	public function get_value() {
		return (object) [
			'module'   => $this->post_id,
			'template' => 'video-a.php'
		];
	}

	public function get_expected() {
		return (object) [
			'module'   => get_post( $this->post_id ),
			'template' => 'video-a.php'
		];
	}

	public function test_property_convert_type() {
		$this->assertSame( 'object', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertSame( [
			'module'   => null,
			'template' => null
		], $this->property->default_value );
	}

	public function test_property_format_value() {
		$this->assertEquals( (object)[
			'module'   => get_post( $this->post_id ),
			'template' => 'pages/properties-page.php'
		], $this->property->format_value( $this->post_id, '', 0 ) );

		$this->assertEquals( (object)[
			'module'   => null,
			'template' => ''
		], $this->property->format_value( 0, '', 0 ) );
	}

	public function test_property_import_value() {
		$expected = [
			'papi_module_test_module'   => $this->post_id,
			'papi_module_test_template' => 'video-a.php',
		];

		$actual = $this->property->import_value( $this->get_value(), $this->property->slug, $this->post_id );

		$this->assertSame( $expected, $actual );
	}

	public function test_property_options() {
		$this->assertSame( 'module', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Module test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_module_test', $this->property->get_option( 'slug' ) );
	}

	public function test_save_properties_value() {
		$_POST[$this->property->slug] = $this->get_value()->module;
		$_POST[$this->property->slug.'_template'] = $this->get_value()->template;
		parent::test_save_properties_value();
	}
}
