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
	 * The data page type.
	 *
	 * @var string
	 */

	private $data_type;

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
	 * @var string
	 */

	public $default_value = '';

	/**
	 * Current property options object.
	 *
	 * @var object
	 */

	private $options;

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
	 * @param string $property_type
	 *
	 * @return object
	 */

	public static function factory( $property_type ) {
		if ( ! is_string( $property_type ) ) {
			return;
		}

		$class_name = papi_get_property_class_name( $property_type );

		if ( empty( $class_name ) || ! class_exists( $class_name ) ) {
			return;
		}

		if ( ! papi()->exists( $class_name ) ) {
			$rc = new ReflectionClass( $class_name );
			$prop = $rc->newInstance();

			// Property class need to be a subclass of Papi Property class.
			if ( ! is_subclass_of( $class_name, __CLASS__ ) ) {
				return null;
			}

			papi()->bind( $class_name, $prop );
		}

		return papi()->make( $class_name );
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
	 * Get default value.
	 *
	 * @return mixed
	 */

	public function get_default_value() {
		return $this->default_value;
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
		return papi_get_post_id();
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
	 * Get property value.
	 *
	 * @return array
	 */

	public function get_value( $fetch_value = true ) {
		if ( ! is_object( $this->options ) ) {
			return;
		}

		if ( $fetch_value && papi_is_empty( $this->options->value ) ) {
			if ( papi_is_option_page() ) {
				$value = papi_option( $this->options->slug );
			} else {
				$value = papi_field( $this->get_post_id(), $this->options->slug );
			}
		} else {
			$value = $this->options->value;
		}

		$type = self::factory( $this->options->type );

		if ( ! is_object( $type ) ) {
			return;
		}

		$default_value = $type->get_default_value();

		if ( is_string( $default_value ) ) {
			$value = papi_convert_to_string( $value );
		}

		if ( papi_is_empty( $value ) ) {
			$value = $default_value;
		}

		if ( ! $this->get_setting( 'allow_html' ) ) {
			$value = papi_santize_data( $value );
		}

		return $value;
	}

	/**
	 * Get the html to display from the property.
	 * This function is required by the property class to have.
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
		return $this->data_type === 'option';
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
		if ( empty( $this->options ) || papi_is_empty( $this->options->description ) ) {
			return;
		}

		?>
		<p><?php echo papi_nl2br( $this->options->description ); ?></p>
	<?php
	}

	/**
	 * Output hidden input field that cointains which property is used.
	 */

	public function render_hidden_html() {
		if ( empty( $this->options ) ) {
			return;
		}

		$slug = $this->options->slug;

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
		<input type="hidden" value="<?php echo $property_serialized; ?>" name="<?php echo $slug; ?>"  data-property="<?php echo $this->options->type; ?>" />
	<?php
	}

	/**
	 * Get label for the property.
	 */

	public function render_label_html() {
		if ( empty( $this->options ) ) {
			return;
		}

		?>
		<label for="<?php echo $this->options->slug; ?>" title="<?php echo $this->options->title . ' ' . papi_require_text( $this->options ); ?>">
			<?php
			echo $this->options->title;
			echo papi_required_html( $this->options );
			?>
		</label>
	<?php
	}

	/**
	 * Render the final html that is displayed in the table.
	 */

	public function render_row_html() {
		if ( empty( $this->options ) ) {
			return;
		}

		if ( $this->options->raw === false ):
			?>
			<tr>
				<?php if ( $this->options->sidebar ): ?>
					<td>
						<?php
							$this->render_label_html();
							$this->render_description_html();
						?>
					</td>
				<?php endif; ?>
				<td <?php echo $this->options->sidebar ? '' : 'colspan="2"'; ?>>
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

		// Get the default settings for the property and merge them with the given settings.
		$options->settings = array_merge( papi_get_property_default_settings( $options->type ), (array) $options->settings );
		$options->settings = (object) $options->settings;

		$options = papi_esc_html( $options, ['html'] );

		// Add default value if database value is empty.
		if ( papi_is_empty( $options->value ) ) {
			$options->value = $options->default;
		}

		return $options;
	}

	/**
	 * Set page data type.
	 *
	 * @param string $data_type
	 */

	public function set_data_type( $data_type ) {
		$this->data_type = $data_type;
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
