<?php

/**
 * Core class that implements a Papi property.
 */
class Papi_Core_Property {

	/**
	 * The conditional class.
	 *
	 * @var Papi_Core_Conditional
	 */
	protected $conditional;

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'string';

	/**
	 * Default import settings.
	 *
	 * @var array
	 */
	private $default_import_settings = [
		'property_array_slugs' => false
	];

	/**
	 * Default options.
	 *
	 * @var array
	 */
	protected $default_options = [
		'after_class'  => '',
		'after_html'   => '',
		'before_class' => '',
		'before_html'  => '',
		'capabilities' => [],
		'default'      => null,
		'description'  => '',
		'disabled'     => false,
		'display'      => true,
		'lang'         => false,
		'layout'       => 'horizontal', // or 'vertical'
		'overwrite'    => false,
		'post_type'    => '',
		'raw'          => false,
		'required'     => false,
		'rules'        => [],
		'settings'     => [],
		'sidebar'      => true,
		'slug'         => '',
		'sort_order'   => -1,
		'title'        => '',
		'type'         => '',
		'value'        => null
	];

	/**
	 * Default value.
	 *
	 * @var null
	 */
	public $default_value;

	/**
	 * Display the property in WordPress admin.
	 *
	 * @var bool
	 */
	protected $display = true;

	/**
	 * Current property options object.
	 *
	 * @var stdClass
	 */
	private $options;

	/**
	 * The post id.
	 *
	 * @var int
	 */
	private $post_id;

	/**
	 * The store that the property works with
	 * to get data.
	 *
	 * @var Papi_Core_Meta_Store
	 */
	private $store;

	/**
	 * Determine if is in a tab.
	 *
	 * @var bool
	 */
	public $tab = false;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup_actions();
		$this->setup_conditional();
		$this->setup_default_options();
		$this->setup_filters();
	}

	/**
	 * Get option value dynamic.
	 *
	 * @param  string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get_option( $key );
	}

	/**
	 * Check if options value exists or not.
	 *
	 * @param  string $key
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		return $this->get_option( $key ) !== null;
	}

	/**
	 * Set options value dynamic.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function __set( $key, $value ) {
		$this->set_option( $key, $value );
	}

	/**
	 * Create a property from options.
	 *
	 * @param  array|object $options
	 *
	 * @return Papi_Property
	 */
	public static function create( $options = [] ) {
		$property = new static;
		$property->set_options( $options );
		return $property;
	}

	/**
	 * Determine if the current user has capabilities rights.
	 *
	 * @return bool
	 */
	public function current_user_can() {
		return papi_current_user_is_allowed( $this->capabilities );
	}

	/**
	 * Delete value from the database.
	 *
	 * @param  string $slug
	 * @param  int    $post_id
	 * @param  string $type
	 *
	 * @return bool
	 */
	public function delete_value( $slug, $post_id, $type ) {
		return papi_delete_property_meta_value( $post_id, $slug, $type );
	}

	/**
	 * Determine if the property is disabled or not.
	 *
	 * @return bool
	 */
	public function disabled() {
		// If the post type don't match the current one
		// the property should not be rendered.
		if ( papi_get_meta_type() === 'post' && ! empty( $this->post_type ) && $this->post_type !== papi_get_post_type() ) {
			return true;
		}

		return $this->disabled;
	}

	/**
	 * Determine if the property should be displayed.
	 *
	 * @return bool
	 */
	public function display() {
		// If the property display is true it can be changed with
		// the display option.
		return $this->display ? $this->options->display : false;
	}

	/**
	 * Create a new instance of the given type.
	 *
	 * @param  mixed $type
	 *
	 * @return object
	 */
	public static function factory( $type ) {
		if ( is_array( $type ) ) {
			$prop = self::create( $type );
			$type = $prop->get_options();
		}

		if ( ! is_string( $type ) && ! is_object( $type ) ) {
			return;
		}

		if ( is_subclass_of( $type, __CLASS__ ) ) {
			return $type;
		}

		$options = null;

		if ( is_object( $type ) ) {
			if ( ! isset( $type->type ) || ! is_string( $type->type ) ) {
				return;
			}

			$options = $type;
			$type = $type->type;
		}

		// Old types, 'PropertyString' => 'String'.
		$type = preg_replace( '/^Property/', '', $type );

		if ( empty( $type ) ) {
			return;
		}

		$class_name = papi_get_property_class_name( $type );

		if ( ! class_exists( $class_name ) || ! is_subclass_of( $class_name, __CLASS__ ) ) {
			return;
		}

		if ( ! papi()->exists( $class_name ) ) {
			papi()->bind( $class_name, new $class_name() );
		}

		$class = papi()->make( $class_name );

		// @codeCoverageIgnoreStart
		if ( ! is_object( $class ) || $class instanceof Papi_Core_Property === false ) {
			$class = new $class_name();
			papi()->bind( $class_name, $class );
		}
		// @codeCoverageIgnoreEnd

		$property = clone $class;

		if ( is_object( $options ) ) {
			$property->set_options( $options );
		}

		return $property;
	}

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function format_value( $value, $slug, $post_id ) {
		$value = maybe_unserialize( $value );

		return papi_maybe_json_decode( $value, $this->convert_type === 'array' );
	}

	/**
	 * Get child property.
	 *
	 * @param  string $slug
	 * @param  array  $items
	 *
	 * @return Papi_Core_Property|null
	 */
	public function get_child_property( $slug, array $items = [] ) {
		$items = empty( $items ) ? $this->get_child_properties() : $items;

		foreach ( $items as $property ) {
			if ( is_array( $property ) && isset( $property['items'] ) ) {
				$property = $this->get_child_property( $slug, $property['items'] );
			}

			$property = papi_get_property_type( $property );

			if ( papi_is_property( $property ) && $property->match_slug( $slug ) ) {
				return $property;
			}
		}
	}

	/**
	 * Get child properties from `items` in the settings array.
	 *
	 * @return array
	 */
	public function get_child_properties() {
		$items = $this->get_setting( 'items', [] );
		return is_array( $items ) ? $items : [$items];
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [];
	}

	/**
	 * Get import settings.
	 *
	 * @return array
	 */
	public function get_import_settings() {
		return [];
	}

	/**
	 * Get meta type from the store or the default one.
	 *
	 * @return string
	 */
	public function get_meta_type() {
		$store = $this->get_store();

		return $store ? $store->get_type() : papi_get_meta_type();
	}

	/**
	 * Get option value.
	 *
	 * @param  string $key
	 *
	 * @return mixed
	 */
	public function get_option( $key ) {
		$value = null;

		if ( isset( $this->options->$key ) ) {
			$value = $this->options->$key;
		}

		if ( papi_is_empty( $value ) && isset( $this->default_options[$key] ) ) {
			$value = $this->default_options[$key];
		}

		if ( $key === 'settings' && is_array( $value ) ) {
			$value = (object) $value;
		} else if ( $key === 'sidebar' && $value ) {
			$value = $this->get_option( 'layout' ) === 'horizontal';
		}

		return $value;
	}

	/**
	 * Get the current property options object.
	 *
	 * @return stdClass
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Get the store that the property will get data from.
	 *
	 * @return Papi_Core_Meta_Store|null
	 */
	public function get_store() {
		if ( $this->store instanceof Papi_Core_Meta_Store ) {
			return $this->store;
		}

		return papi_get_meta_store( $this->get_post_id() );
	}

	/**
	 * Get post id.
	 *
	 * @return int
	 */
	public function get_post_id() {
		if ( ! papi_is_empty( $this->post_id ) ) {
			return $this->post_id;
		}

		if ( $this->store instanceof Papi_Core_Meta_Store ) {
			return $this->store->id;
		}

		return papi_get_post_id();
	}

	/**
	 * Get conditional rules.
	 *
	 * @return array
	 */
	public function get_rules() {
		return $this->get_option( 'rules' );
	}

	/**
	 * Get setting value.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 *
	 * @return stdClass
	 */
	public function get_setting( $key, $default = null ) {
		if ( ! is_string( $key ) ) {
			return $default;
		}

		$settings = $this->get_settings();

		if ( isset( $settings->$key ) ) {
			return $settings->$key;
		}

		return $default;
	}

	/**
	 * Get custom property settings.
	 *
	 * @return stdClass
	 */
	public function get_settings() {
		$settings = wp_parse_args(
			$this->get_option( 'settings' ),
			$this->get_default_settings()
		);

		return (object) $settings;
	}

	/**
	 * Get property slug.
	 *
	 * @param  bool $remove_prefix
	 *
	 * @return string
	 */
	public function get_slug( $remove_prefix = false ) {
		if ( $remove_prefix ) {
			return unpapify( $this->get_option( 'slug' ) );
		}

		return $this->get_option( 'slug' );
	}

	/**
	 * Get value, no database connections here.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return papi_is_empty( $this->value ) ? $this->default : $this->value;
	}

	/**
	 * Get the html id attribute value.
	 *
	 * @param  object|string $suffix
	 * @param  int $row
	 *
	 * @return string
	 */
	public function html_id( $suffix = '', $row = null ) {
		if ( is_array( $suffix ) || is_object( $suffix ) ) {
			return papi_f( $this->html_name( $suffix, $row ) );
		} else {
			$suffix = empty( $suffix ) || ! is_string( $suffix ) ? '' : '_' . $suffix;
			$suffix = papi_underscorify( papi_slugify( $suffix ) );
		}

		$name = $this->html_name();
		$len  = strlen( $name );

		if ( isset( $name[$len - 1] ) && $name[$len - 1] === ']' ) {
			return papi_f( sprintf( '%s%s]', substr( $name, 0, $len - 1 ), $suffix ) );
		}

		return papi_f( sprintf( '%s%s', $this->html_name(), $suffix ) );
	}

	/**
	 * Get html name for property with or without sub property and row number.
	 *
	 * @param  array|object $sub_property
	 * @param  int $row
	 *
	 * @return string
	 */
	public function html_name( $sub_property = null, $row = null ) {
		$base_slug = $this->get_option( 'slug' );

		if ( is_null( $sub_property ) ) {
			return $base_slug;
		}

		if ( is_numeric( $row ) ) {
			$base_slug = sprintf( '%s[%d]', $base_slug, intval( $row ) );
		}

		if ( ! papi_is_property( $sub_property ) ) {
			if ( is_array( $sub_property ) || is_object( $sub_property ) ) {
				$sub_property = self::factory( $sub_property );
			} else {
				return $base_slug;
			}
		}

		return sprintf(
			'%s[%s]',
			$base_slug,
			unpapify( $sub_property->get_slug() )
		);
	}

	/**
	 * Get the import settings.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 *
	 * @return mixed
	 */
	public function import_setting( $key, $default = null ) {
		if ( ! is_string( $key ) ) {
			return $default;
		}

		$settings = $this->import_settings();

		return isset( $settings->$key ) ? $settings->$key : $default;
	}

	/**
	 * Get the import settings.
	 *
	 * @return object
	 */
	public function import_settings() {
		$settings = $this->get_import_settings();
		$settings = is_array( $settings ) || is_object( $settings ) ? $settings : [];

		return (object) array_merge(
			$this->default_import_settings,
			(array) $settings
		);
	}

	/**
	 * Import value to the property.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function import_value( $value, $slug, $post_id ) {
		if ( ! ( $value = $this->prepare_value( $value ) ) ) {
			return;
		}

		$value = maybe_unserialize( $value );

		return papi_maybe_json_decode( $value, $this->convert_type === 'array' );
	}

	/**
	 * Change value after it's loaded from the database.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function load_value( $value, $slug, $post_id ) {
		$value = maybe_unserialize( $value );

		return papi_maybe_json_decode( $value, $this->convert_type === 'array' );
	}

	/**
	 * Match property slug with given slug value.
	 *
	 * @param  string $slug
	 *
	 * @return bool
	 */
	public function match_slug( $slug ) {
		if ( ! is_string( $slug ) ) {
			$slug = '';
		}

		return $this->get_slug( ! preg_match( '/^papi\_/', $slug ) ) === $slug;
	}

	/**
	 * Prepare value before database.
	 *
	 * @param  mixed $value
	 *
	 * @return mixed
	 */
	protected function prepare_value( $value ) {
		if ( papi_is_empty( $value ) ) {
			return;
		}

		$value = papi_santize_data( $value );

		if ( is_array( $value ) ) {
			$value = array_filter( $value, function ( $val ) {
				return ! papi_is_empty( $val );
			} );

			if ( ! count( array_filter( array_keys( $value ), 'is_string' ) ) ) {
				$value = array_values( $value );
			}
		}

		return $value;
	}

	/**
	 * Render AJAX request.
	 */
	public function render_ajax_request() {
		papi_render_property( $this );
	}

	/**
	 * Check if the property is allowed
	 * to render by the conditional rules.
	 *
	 * @param  array $rules
	 *
	 * @return bool
	 */
	public function render_is_allowed_by_rules( array $rules = [] ) {
		if ( empty( $rules ) ) {
			$rules = $this->get_rules();
		}

		return $this->conditional->display( $rules, $this );
	}

	/**
	 * Set the store that the property will get data from.
	 *
	 * @param Papi_Core_Meta_Store $store
	 */
	public function set_store( Papi_Core_Meta_Store $store ) {
		$this->store = $store;
	}

	/**
	 * Set post id.
	 *
	 * @param int $post_id
	 */
	public function set_post_id( $post_id ) {
		if ( ! is_numeric( $post_id ) ) {
			return;
		}

		$this->post_id = (int) $post_id;
	}

	/**
	 * Set the current property options object.
	 *
	 * @param array|object $options
	 */
	public function set_options( $options ) {
		$this->options = $this->setup_options( $options );
	}

	/**
	 * Set property option value.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set_option( $key, $value ) {
		if ( ! is_object( $this->options ) ) {
			$this->options = (object) $this->default_options;
		}

		$this->options->$key = $value;
	}

	/**
	 * Set property setting value.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set_setting( $key, $value ) {
		if ( isset( $this->options->settings, $this->options->settings->$key ) ) {
			$this->options->settings->$key = $value;
		}
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
	}

	/**
	 * Setup conditional class.
	 */
	protected function setup_conditional() {
		$this->conditional = new Papi_Core_Conditional();
	}

	/**
	 * Setup default options values.
	 * All default values can't be set in the `$default_options` array.
	 */
	private function setup_default_options() {
		if ( $this->default_options['sort_order'] === -1 ) {
			$this->default_options['sort_order'] = papi_filter_settings_sort_order();
		}

		if ( empty( $this->default_options['post_type'] ) ) {
			$this->default_options['post_type'] = papi_get_post_type();
		}

		if ( papi_is_empty( $this->default_options['default'] ) ) {
			$this->default_options['default'] = $this->default_value;
		}
	}

	/**
	 * Setup filters.
	 */
	protected function setup_filters() {
	}

	/**
	 * Setup property options.
	 *
	 * @param  mixed $options
	 *
	 * @return mixed
	 */
	private function setup_options( $options ) {
		// When a object is sent in, just return it.
		if ( is_object( $options ) ) {
			return $options;
		}

		// Only arrays can be handled.
		if ( ! is_array( $options ) ) {
			$options = [];
		}

		// Merge default options with the given options array.
		$options = array_merge( $this->default_options, $options );
		$options = (object) $options;

		// Capabilities should be a array.
		$options->capabilities = papi_to_array( $options->capabilities );

		// Setup property slug.
		$options->slug = $this->setup_options_slug( $options );

		// Setup property settings.
		$options->settings = $this->setup_options_settings( $options );

		// Type should always be lowercase.
		$options->type = strtolower( $options->type );

		// Escape all options except those that are send it as second argument.
		return papi_esc_html( $options, ['before_html', 'html', 'after_html'] );
	}

	/**
	 * Setup options slug.
	 *
	 * @param  stdClass $options
	 *
	 * @return string
	 */
	private function setup_options_slug( $options ) {
		$slug = $options->slug;

		// When `slug` is false a unique slug should be generated.
		if ( $slug === false ) {
			return '_' . papi_html_name( md5( uniqid( rand(), true ) ) );
		}

		// If `slug` is empty, check if `title` is not empty
		// and generate a slug from the `title` or if empty
		// use the `type`.
		if ( empty( $slug ) ) {
			if ( empty( $options->title ) ) {
				$slug = papi_slugify( $options->type );
			} else {
				$slug = papi_slugify( $options->title );
			}
		}

		// Create a html friendly name from the `slug`.
		return papi_html_name( $slug );
	}

	/**
	 * Setup options settings.
	 *
	 * @param  stdClass $options
	 *
	 * @return stdClass
	 */
	private function setup_options_settings( $options ) {
		$property_class = self::factory( $options->type );

		if ( papi_is_property( $property_class ) ) {
			$options->settings = array_merge(
				(array) $property_class->get_default_settings(),
				(array) $options->settings
			);
		}

		return (object) $options->settings;
	}

	/**
	 * Update value before it's saved to the database.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function update_value( $value, $slug, $post_id ) {
		if ( ! ( $value = $this->prepare_value( $value ) ) ) {
			return;
		}

		return papi_maybe_json_encode( $value );
	}
}
