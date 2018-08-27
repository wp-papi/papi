<?php

/**
 * @group core
 */
class Papi_Core_Type_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->info_core_path  = PAPI_FIXTURE_DIR . '/core-types/info-core-type.php';
		$this->info2_core_path = PAPI_FIXTURE_DIR . '/core-types/info2-core-type.php';
		$this->info_core_type  = new Papi_Core_Type( $this->info_core_path );

		if ( ! class_exists( 'Info_Core_Type' ) ) {
			require_once $this->info_core_path;
		}

		if ( ! class_exists( 'Info2_Core_Type' ) ) {
			require $this->info2_core_path;
		}
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$this->info_core_path,
			$this->info2_core_type,
			$this->info_core_type
		);
	}

	public function test_core_type_allowed() {
		$this->assertTrue( $this->info_core_type->allowed() );
	}

	public function test_core_type_get_id() {
		$this->assertRegExp( '/\/core-types\/info-core-type$/', $this->info_core_type->get_id() );
	}

	public function test_core_type_get_meta() {
		$class = new Info_Core_Type( $this->info_core_path );
		$this->assertSame( 'Info core type', $class->name );
		$this->assertSame( 500, $class->sort_order );

		$class2 = new Info2_Core_Type( $this->info2_core_path );
		$this->assertSame( 'Info2 core type', $class2->name );
		$this->assertSame( 500, $class2->sort_order );
	}

	public function test_core_type_get_meta_abstract() {
		$path = PAPI_FIXTURE_DIR . '/core-types/abstract-core-type.php';

		if ( ! class_exists( 'Abstract_Core_Type' ) ) {
			require_once $path;
		}

		$class = new Abstract_Core_Type( $path );
		$this->assertSame( 'Abstract core', $class->name );
	}

	public function test_core_type_get_type() {
		$this->assertSame( 'core', $this->info_core_type->get_type() );
	}

	public function test_core_type_has_name() {
		$this->assertFalse( $this->info_core_type->has_name() );
		$class = new Info_Core_Type( $this->info_core_path );
		$this->assertTrue( $class->has_name() );
	}

	public function test_core_type_match_id() {
		$this->assertTrue( $this->info_core_type->match_id( $this->info_core_type->get_id() ) );
		$this->assertFalse( $this->info_core_type->match_id( '' ) );
	}

	public function test_core_type_new_class() {
		$class = $this->info_core_type->new_class();
		$this->assertInstanceOf( 'Papi_Core_Type', $class );
	}

	public function test_set_id() {
		$class = new Papi_Core_Type;
		$this->assertEmpty( $class->get_id() );

		$class->set_id( 'hello' );
		$this->assertSame( 'hello', $class->get_id() );
	}
}
