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
	 * Setup tabs array.
	 *
	 * @param array $tabs
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_tabs( $tabs ) {
		// Check capabilities on tabs.
		$tabs = array_filter( $tabs, function ( $tab ) {
			return _papi_current_user_is_allowed( $tab->options->capabilities );
		} );

		// Sort tabs based on `sort_order` value.
		$tabs = _papi_sort_order( $tabs );

		// Generate unique names for all tabs.
		for ( $i = 0; $i < count( $tabs ); $i ++ ) {
			$tabs[ $i ]->name = _papi_name( $tabs[ $i ]->title ) . '_' . $i;
		}

		$this->tabs = $tabs;
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
						<a href="#" data-papi-tab="<?php echo $tab->name; ?>">
							<?php if ( isset( $tab->options->icon ) && ! empty( $tab->options->icon ) ): ?>
								<img src="<?php echo $tab->options->icon; ?>" alt="<?php echo $tab->title; ?>"/>
							<?php endif;
							echo $tab->title; ?>
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
					     data-papi-tab="<?php echo $tab->name; ?>">
						<?php
							$properties = $tab->properties;

							$properties = array_map(function ($property) {
								// While in a tab the sidebar is required.
								$property->sidebar = true;

								return $property;
							}, $properties);

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
}
