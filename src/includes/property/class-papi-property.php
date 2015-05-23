<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property.
 *
 * @package Papi
 */

class Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */

	public $convert_type = 'string';

	/**
	 * The page options.
	 *
	 * @var array
	 */

	private $page_options = [
		'data_type' => '',
		'post_id'   => 0
	];

	/**
	 * Default options.
	 *
	 * @var array
	 */

	private $default_options = [
		'array_slug'   => '',
		'capabilities' => [],
		'default'      => '',
		'description'  => '',
		'disabled'     => false,
		'lang'         => false,
		'raw'          => false,
		'required'     => false,
		'settings'     => [],
		'sidebar'      => true,
		'slug'         => '',
		'sort_order'   => -1,
		'title'        => '',
		'type'         => '',
		'value'        => ''
	];

	/**
	 * Default value.
	 *
	 * @var null
	 */

	public $default_value;

	/**
	 * Current property options object.
	 *
	 * @var object
	 */

	private $options;

	/**
	 * Get option value dynamic.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */

	public function __get( $key ) {
		return $this->get_option( $key );
	}

	/**
	 * Constructor.
	 */

	public function __construct() {
		$this->setup_actions();
		$this->setup_filters();
	}

	/**
	 * Convert settings items to properties if they are a property.
	 *
	 * @param array $settings
	 *
	 * @return array
	 */

	private function convert_settings( $settings ) {
		foreach ( $settings as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( isset( $value['type'] ) ) {
					$type = papi_get_property_class_name( $value['type'] );
					if ( class_exists( $type ) ) {
						$settings[$key] = papi_property( $value );
					} else {
						$settings[$key] = $this->convert_settings( $value );
					}
				} else {
					$settings[$key] = $this->convert_settings( $value );
				}
			}
		}

		return $settings;
	}

	/**
	 * Create a property from options.
	 *
	 * @param array|object $options
	 *
	 * @return Papi_Property
	 */

	public static function create( $options = [] ) {
		$property = new self;
		$property->set_options( $options );
		return $property;
	}

	/**
	 * Get default options.
	 *
	 * @return array
	 */

	public static function default_options() {
		$property = new self;
		$default_options = $property->default_options;

		if ( $default_options['sort_order'] === -1 ) {
			$default_options['sort_order'] = papi_filter_settings_sort_order();
		}

		return $default_options;
	}

	/**
	 * Create a new instance of the given property.
	 *
	 * @param string $type
	 *
	 * @return object
	 */

	public static function factory( $type ) {
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

		$type = preg_replace( '/^Property/', '', $type );

		$class_name = papi_get_property_class_name( $type );

		if ( ! class_exists( $class_name ) || ! is_subclass_of( $class_name, __CLASS__ ) ) {
			return;
		}

		$property = new $class_name();

		if ( is_object( $options ) ) {
			$property->set_options( $options );
		}

		return $property;
	}

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return mixed
	 */

	public function format_value( $value, $slug, $post_id ) {
		return $value;
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
	 * Get option value.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */

	public function get_option( $key ) {
		if ( isset( $this->options->$key ) ) {
			return $this->options->$key;
		}

		if ( isset( $this->default_options[$key] ) ) {
			return $this->default_options[$key];
		}
	}

	/**
	 * Get the current property options object.
	 *
	 * @return object
	 */

	public function get_options() {
		return $this->options;
	}

	/**
	 * Get post id.
	 *
	 * @return int
	 */

	public function get_post_id() {
		if ( $this->page_options['post_id'] === 0 ) {
			return papi_get_post_id();
		}

		return $this->page_options['post_id'];
	}

	/**
	 * Get setting value.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */

	public function get_setting( $key ) {
		$settings = $this->get_settings();
		if ( isset( $settings->$key ) ) {
			return $settings->$key;
		}
	}

	/**
	 * Get custom property settings.
	 *
	 * @return object
	 */

	public function get_settings() {
		if ( ! is_object( $this->options ) ) {
			return;
		}

		$settings = wp_parse_args( $this->options->settings, $this->get_default_settings() );

		return (object) $this->convert_settings( $settings );
	}

	/**
	 * Get property type.
	 *
	 * @return string
	 */

	public function get_type() {
		return $this->get_option( 'type' );
	}

	/**
	 * Get property value.
	 *
	 * @return array
	 */

	public function get_value() {
		if ( ! is_object( $this->options ) ) {
			return;
		}

		$value = $this->get_option( 'value' );

		if ( papi_is_empty( $value ) ) {
			$slug = papi_remove_papi( $this->get_option( 'slug' ) );

			if ( papi_is_option_page() ) {
				$value = papi_option( $slug );
			} else {
				$value = papi_field( $this->get_post_id(), $slug );
			}
		}

		return $this->prepare_value( $value );
	}

	/**
	 * Prepare property value.
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */

	public function prepare_value( $value ) {
		if ( papi_is_empty( $value ) ) {
			return $this->default_value;
		}

		if ( $this->convert_type === 'string' ) {
			$value = papi_convert_to_string( $value );
		}

		if ( ! $this->get_setting( 'allow_html' ) ) {
			$value = papi_santize_data( $value );
		}

		return $value;
	}

	/**
	 * Check so the property has a type.
	 *
	 * @return bool
	 */

	public function has_type() {
		return empty( $this->get_option( 'type' ) );
	}

	/**
	 * Get the html to display from the property.
	 *
	 * @return mixed
	 */

	public function html() {
	}

	/**
	 * Get html name for property with or without sub property and row number.
	 *
	 * @param array|object $sub_property
	 * @param int $row
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

		if ( ! ( $sub_property instanceof Papi_Property ) ) {
			if ( is_array( $sub_property ) || is_object( $sub_property ) ) {
				$sub_property = self::create( $sub_property );
			} else {
				return $base_slug;
			}
		}

		return sprintf( '%s[%s]', $base_slug, papi_remove_papi( $sub_property->get_option( 'slug' ) ) );
	}

	/**
	 * Check if it's a option page or not.
	 *
	 * @return bool
	 */

	public function is_option_page() {
		return $this->page_options['data_type'] === 'option';
	}

	/**
	 * Change value after it's loaded from the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return mixed
	 */

	public function load_value( $value, $slug, $post_id ) {
		return $value;
	}

	/**
	 * Override property options.
	 *
	 * @return array
	 */

	public function override_property_options() {
		return [];
	}

	/**
	 * Render the property description.
	 */

	public function render_description_html() {
		if ( papi_is_empty( $this->get_option( 'description' )  ) ) {
			return;
		}

		?>
		<p><?php echo papi_nl2br( $this->get_option( 'description' ) ); ?></p>
	<?php
	}

	/**
	 * Output hidden input field that cointains which property is used.
	 */

	public function render_hidden_html() {
		$slug = $this->get_option( 'slug' );

		if ( substr( $slug, - 1 ) === ']' ) {
			$slug = substr( $slug, 0, - 1 );
			$slug = papi_get_property_type_key( $slug );
			$slug .= ']';
		} else {
			$slug = papi_get_property_type_key( $slug );
		}

		$slug = papify( $slug );

		$property_serialized = base64_encode( serialize( $this->options ) );

		?>
		<input type="hidden" value="<?php echo $property_serialized; ?>" name="<?php echo $slug; ?>"  data-property="<?php echo $this->get_option( 'type' ); ?>" />
	<?php
	}

	/**
	 * Get label for the property.
	 */

	public function render_label_html() {
		$title = $this->get_option( 'title' );
		?>
		<label for="<?php echo $this->get_option( 'slug' ); ?>" title="<?php echo $title . ' ' . papi_require_text( $this->options ); ?>">
			<?php
			echo $title;
			echo papi_required_html( $this->options );
			?>
		</label>
	<?php
	}

	/**
	 * Render the final html that is displayed in the table.
	 */

	public function render_row_html() {
		if ( ! $this->get_option( 'raw' ) ):
			?>
			<tr>
				<?php if ( $this->get_options( 'sidebar' ) ): ?>
					<td>
						<?php
							$this->render_label_html();
							$this->render_description_html();
						?>
					</td>
				<?php endif; ?>
				<td <?php echo $this->get_options( 'sidebar' ) ? '' : 'colspan="2"'; ?>>
					<?php $this->html(); ?>
				</td>
			</tr>
		<?php
		else :
			$this->html();
		endif;
	}

	/**
	 * Setup actions.
	 */

	protected function setup_actions() {
	}

	/**
	 * Setup filters.
	 */

	protected function setup_filters() {
	}

	/**
	 * Setup options.
	 *
	 * @param array|object $options
	 */

	private function setup_options( $options ) {
		if ( ! is_array( $options ) ) {
			return $options;
		}

		$options = array_merge( $this->default_options, $options );
		$options = (object) $options;

		if ( $options->sort_order === -1 ) {
			$options->sort_order = papi_filter_settings_sort_order();
		}

		$options->capabilities = papi_to_array( $options->capabilities );

		// Generate random slug if we don't have a title or slug.
		if ( empty( $options->title ) && empty( $options->slug ) ) {
			if ( empty( $options->type ) ) {
				$options->slug = papi_slugify( uniqid() );
			} else {
				$options->slug = papi_slugify( $options->type );
			}
		}

		// Generate slug from title.
		if ( empty( $options->slug ) ) {
			$options->slug = papi_slugify( $options->title );
		}

		// Generate a vaild Papi meta name for slug.
		$options->array_slug = $options->slug = papi_html_name( $options->slug );

		$type_class = self::factory( $options->type );

		if ( is_object( $type_class ) ) {
			$options->settings = array_merge( (array) $type_class->get_default_settings(), (array) $options->settings );
		}

		$options->settings = (object) $options->settings;

		$options = papi_esc_html( $options, ['html'] );

		// Add default value if database value is empty.
		if ( papi_is_empty( $options->value ) ) {
			$options->value = $options->default;
		}

		return $options;
	}

	/**
	 * Set page options.
	 *
	 * @param array $page_options
	 */

	public function set_page_options( array $page_options = [] ) {
		$this->page_options = array_merge( $this->page_options, $page_options );
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
	 * Set property value.
	 *
	 * @param mixed $value
	 */

	public function set_option( $key, $value ) {
		if ( ! is_object( $this->options ) ) {
			$this->options = (object) $this->default_options;
		}

		if ( isset( $this->options->$key ) ) {
			$this->options->$key = $value;
		}
	}

	/**
	 * Update value before it's saved to the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return mixed
	 */

	public function update_value( $value, $slug, $post_id ) {
		return $value;
	}
}
