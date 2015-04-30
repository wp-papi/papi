<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property.
 *
 * @package Papi
 * @since 1.3.0
 */

class Papi_Property {

	/**
	 * Default options.
	 *
	 * @var array
	 * @since 1.3.0
	 */

	private $default_options = array(
		'allow_html'   => false,
		'array_slug'   => '',
		'capabilities' => array(),
		'default'      => '',
		'description'  => '',
		'disabled'     => false,
		'lang'         => false,
		'raw'          => false,
		'settings'     => array(),
		'sidebar'      => true,
		'slug'         => '',
		'sort_order'   => -1,
		'required'     => false,
		'title'        => '',
		'type'         => '',
		'value'        => ''
	);

	/**
	 * Default value.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $default_value = '';

	/**
	 * The page post id.
	 *
	 * @var int
	 * @since 1.0.0
	 */

	protected $post_id;

	/**
	 * Current property options object.
	 *
	 * @var object
	 * @since 1.0.0
	 */

	private $options;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */

	public function __construct( $post_id = null ) {
		$this->post_id = $post_id;
		$this->setup_actions();
		$this->setup_filters();
	}

	/**
	 * Create a property from options.
	 *
	 * @param array $options
	 * @since 1.0.0
	 *
	 * @return Papi_Property
	 */

	public static function create( $options ) {
		$property = new self;
		$property->set_options( $options );
		return $property;
	}

	/**
	 * Create a new instance of the given property.
	 *
	 * @param string $property_type
	 * @since 1.0.0
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
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public function format_value( $value, $slug, $post_id ) {
		return $value;
	}

	/**
	 * Get default options.
	 *
	 * @since 1.0.0
	 * @return array
	 */

	public static function get_default_options() {
		$property = new self;
		return $property->default_options;
	}

	/**
	 * Get default settings.
	 *
	 * @since 1.2.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array();
	}

	/**
	 * Get post id.
	 *
	 * @since 1.3.0
	 *
	 * @return int
	 */

	public function get_post_id() {
		if ( empty( $this->post_id ) ) {
			return papi_get_post_id();
		}

		return $this->post_id;
	}

	/**
	 * Get property value.
	 *
	 * @since 1.3.0
	 * @return array
	 */

	public function get_value( $fetch_value = true ) {
		if ( ! is_object( $this->options ) ) {
			return;
		}

		if ( $fetch_value && papi_is_empty( $this->options->value ) ) {
			$post_id = $this->get_post_id();
			$value = papi_field( $post_id, $this->options->slug, null, true );
		} else {
			$value = $this->options->value;
		}

		if ( is_string( $this->default_value ) ) {
			$value = papi_convert_to_string( $value );
		}

		if ( papi_is_empty( $value ) ) {
			$value = $this->default_value;
		}

		if ( ! $this->get_option( 'allow_html' ) ) {
			$value = papi_santize_data( $value );
		}

		return $value;
	}

	/**
	 * Get option value.
	 *
	 * @param string $key
	 * @since 1.3.0
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
	 * @since 1.0.0
	 *
	 * @return object
	 */

	public function get_options() {
		return $this->options;
	}

	/**
	 * Get custom property settings.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */

	public function get_settings() {
		if ( ! is_object( $this->options ) ) {
			return;
		}

		return (object) wp_parse_args( $this->options->settings, $this->get_default_settings() );
	}

	/**
	 * Get the html to display from the property.
	 * This function is required by the property class to have.
	 *
	 * @since 1.0.0
	 */

	public function html() {
	}

	/**
	 * This filter is applied after the $value is loaded in the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public function load_value( $value, $slug, $post_id ) {
		return $value;
	}

	/**
	 * Override property options.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */

	public function override_property_options() {
		return array();
	}

	/**
	 * Register assets actions.
	 *
	 * @since 1.0.0
	 */

	public function render_assets_html() {
		if ( method_exists( $this, 'css' ) ) {
			add_action( 'admin_head', array( $this, 'css' ) );
		}

		if ( method_exists( $this, 'js' ) ) {
			add_action( 'admin_footer', array( $this, 'js' ) );
		}
	}

	/**
	 * Render the property description.
	 *
	 * @since 1.0.0
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
	 *
	 * @since 1.0.0
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
	 *
	 * @since 1.0.0
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
	 *
	 * @since 1.0.0
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
	 *
	 * @since 1.3.0
	 */

	protected function setup_actions() {
	}

	/**
	 * Setup filters.
	 *
	 * @since 1.3.0
	 */

	protected function setup_filters() {
	}

	/**
	 * Setup options.
	 *
	 * @param array|object $options
	 * @since 1.3.0
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

		// Generate a valid Papi meta name for old slug.
		if ( ! empty( $options->old_slug ) ) {
			$options->old_slug = papi_html_name( $options->old_slug );
		}

		// Get the default settings for the property and merge them with the given settings.
		$options->settings = array_merge( papi_get_property_default_settings( $options->type ), (array) $options->settings );
		$options->settings = (object) $options->settings;

		$options = papi_esc_html( $options, array( 'html' ) );

		// Add default value if database value is empty.
		if ( papi_is_empty( $options->value ) ) {
			$options->value = $options->default;
		}

		return $options;
	}

	/**
	 * Set the current property options object.
	 *
	 * @param array|object $options
	 * @since 1.0.0
	 */

	public function set_options( $options ) {
		$this->options = $this->setup_options( $options );
	}

	/**
	 * Set property value.
	 *
	 * @param mixed $value
	 * @since 1.3.0
	 */

	public function set_option( $key, $value ) {
		if ( isset( $this->options->$key ) ) {
			$this->options->$key = $value;
		}
	}

	/**
	 * This filter is applied before the $value is saved in the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public function update_value( $value, $slug, $post_id ) {
		return $value;
	}
}
