<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Admin Meta Box Tabs.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Admin_Meta_Box_Tabs {

	/**
	 * Default options.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	private $default_options = array(
		'capabilities' => array(),
		'icon'         => '',
		'sort_order'   => _papi_get_option( 'sort_order', 1000 ),
		// Private options
		'_name'        => ''
	);

	/**
	 * The tabs.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	private $tabs = array();

	/**
	 * Constructor.
	 *
	 * @param array $tabs
	 *
	 * @since 1.0.0
	 */

	public function __construct( $tabs = array() ) {
		if ( empty( $tabs ) ) {
			return;
		}

		$this->setup_tabs( $tabs );

		$this->html();
	}

	/**
	 * Generate html for tabs and properties.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function html() {
		?>
		<div class="papi-tabs-wrapper">
			<div class="papi-tabs-table-back"></div>
			<div class="papi-tabs-back"></div>
			<ul class="papi-tabs">
				<?php

				foreach ( $this->tabs as $tab ):
					?>
					<li class="<?php echo $this->tabs[0] == $tab ? 'active' : ''; ?>">
						<a href="#" data-papi-tab="<?php echo $tab->options->_name; ?>">
							<?php if ( ! empty( $tab->options->icon ) ): ?>
								<img src="<?php echo $tab->options->icon; ?>" alt="<?php echo $tab->options->title; ?>"/>
							<?php endif;
							echo $tab->options->title; ?>
						</a>
					</li>
				<?php
				endforeach;
				?>
			</ul>
			<div class="papi-tabs-content">
				<?php
				foreach ( $this->tabs as $tab ):
					?>
					<div class="<?php echo $this->tabs[0] == $tab ? 'active' : ''; ?>"
					     data-papi-tab="<?php echo $tab->options->_name; ?>">
						<?php

							$properties = _papi_populate_properties( $tab->properties );

							$properties = array_map( function ( $property ) {
								// While in a tab the sidebar is required.
								$property->sidebar = true;

								return $property;
							}, $properties );

							_papi_render_properties( $properties );
						?>
					</div>
				<?php
				endforeach;
				?>
			</div>
		</div>
		<div class="papi-clear"></div>
	<?php
	}

	/**
	 * Setup tabs array.
	 *
	 * @param array $tabs
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_tabs( $tabs ) {
		$_tabs = array();

		foreach ( $tabs as $tab ) {
			$tab = (object)$tab;

			if ( ! isset( $tab->options ) ) {
				continue;
			}

			$tab->options = $this->setup_options( $tab->options );

			if ( _papi_current_user_is_allowed( $tab->options->capabilities ) ) {
				$_tabs[] = array(
					'sort_order' => $tab->options->sort_order,
					$tab
				);
			}
		}

		// Sort tabs based on `sort_order` value.
		$tabs = _papi_sort_order( $_tabs );

		$tabs = array_map( function ( $tab ) {
			if ( is_array( $tab ) && isset( $tab[0] ) ) {
				return $tab[0];
			} else {
				return array();
			}
		}, $tabs );

		// Generate unique names for all tabs.
		for ( $i = 0; $i < count( $tabs ); $i ++ ) {

			if ( empty( $tabs[$i] ) ) {
				continue;
			}

			$tabs[ $i ]->options->_name = _papi_html_name( $tabs[$i]->options->title ) . '_' . $i;
		}

		$this->tabs = $tabs;
	}

	/**
	 * Setup options.
	 *
	 * @param array $options
	 */

	private function setup_options( $options ) {
		$options = array_merge( $this->default_options, $options );
		$options = (object)$options;
		return $options;
	}
}
