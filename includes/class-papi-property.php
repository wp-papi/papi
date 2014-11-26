<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Property {

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
	 * @access private
	 */

	private $post_id;

	/**
	 * Current property options object that is used to generate a property.
	 *
	 * @var object
	 * @since 1.0.0
	 * @access private
	 */

	private $options;

	/**
	 * Setup the current post id.
	 *
	 * @since 1.0.0
	 */

	public function __construct() {
		$this->post_id = _papi_get_post_id();
	}

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	 public function get_default_settings() {
		return array();
	 }

	/**
	 * Create a new instance of the given property.
	 *
	 * @param string $property_type
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */

	public static function factory( $property_type ) {
		if ( ! is_string( $property_type ) ) {
			return null;
		}

		$property_type = _papi_get_property_class_name( $property_type );

		if ( empty( $property_type ) || ! class_exists( $property_type ) ) {
			return null;
		}

		$prop = new $property_type();

		// Property class need to be a subclass of Papi Property class.
		if ( ! is_subclass_of( $prop, 'Papi_property' ) ) {
			return null;
		}

		return $prop;
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
	 * Output custom css for property
	 *
	 * @since 1.0.0
	 */

	public function css() {
	}

	/**
	 * Output custom js for property
	 *
	 * @since 1.0.0
	 */

	public function js() {
	}

	/**
	 * Register assets actions.
	 *
	 * @since 1.0.0
	 */

	public function assets() {
		add_action( 'admin_head', array( $this, 'css' ) );
		add_action( 'admin_footer', array( $this, 'js' ) );
	}

	/**
	 * Output hidden input field that cointains which property is used.
	 *
	 * @since 1.0.0
	 */

	public function hidden() {
		$slug = $this->options->slug;

		if ( substr( $slug, - 1 ) === ']' ) {
			$slug = substr( $slug, 0, - 1 );
			$slug = _papi_get_property_type_key( $slug );
			$slug .= ']';
		} else {
			$slug = _papi_get_property_type_key( $slug );
		}

		$slug = _papify( $slug );

		?>
		<input type="hidden" value="<?php echo $this->options->type; ?>" name="<?php echo $slug; ?>"/>
	<?php
	}

	/**
	 * Get label for the property.
	 *
	 * @since 1.0.0
	 */

	public function label() {
		$title = $this->options->title;

		if ( $this->options->required ) {
			$title .= ' ' . __( '(required field)', 'papi' );
		}

		?>
		<label for="<?php echo $this->options->slug; ?>" title="<?php echo $title; ?>">
			<?php

			echo $this->options->title;

			if ( $this->options->required ):
				?>
				<span class="papi-rq">*</span>
			<?php
			endif;

			?>
		</label>
	<?php
	}

	/**
	 * Get help text for property.
	 *
	 * @since 1.0.0
	 */

	public function instruction() {
		if ( empty( $this->options->instruction ) ) {
			return;
		}

		?>
		<p><?php echo strip_tags( $this->options->instruction ); ?></p>
	<?php
	}

	/**
	 * Render the final html that is displayed in the table.
	 *
	 * @since 1.0.0
	 */

	public function render() {
		if ( $this->options->raw === false ):
			?>
			<tr>
				<?php if ( $this->options->sidebar ): ?>
					<td>
						<?php
						$this->label();
						$this->instruction();
						?>
						</td>
				<?php endif; ?>
				<td <?php echo $this->options->sidebar ? '' : 'colspan="2"'; ?>>
					<?php $this->html(); ?>
				</td>
			</tr>
		<?php
		else:
			$this->html();
		endif;
	}

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public function format_value( $value, $slug, $post_id ) {
		return $value;
	}

	/**
	 * This filter is applied after the $value is loaded in the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public function load_value( $value, $slug, $post_id ) {
		return $value;
	}

	/**
	 * This filter is applied before the $value is saved in the database.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public function update_value( $value, $slug, $post_id ) {
		return $value;
	}

	/**
	 * Get the current property options object.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */

	public function get_options() {
		return $this->options;
	}

	/**
	 * Set the current property options object.
	 *
	 * @param object $options
	 *
	 * @since 1.0.0
	 */

	public function set_options( $options ) {
		$this->options = $options;
	}

	/**
	 * Get database value.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public function get_value() {
		$value = $this->options->value;

		if ( is_string( $this->default_value ) ) {
			$value = _papi_convert_to_string( $value );
		}

		if ( empty( $value ) ) {
			$value = $this->default_value;
		}

		return $this->load_value( $value, $this->options->slug, $this->post_id );
	}

	/**
	 * Get custom property settings.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */

	public function get_settings() {
		return (object) wp_parse_args( $this->options->settings, $this->get_default_settings() );
	}
}
