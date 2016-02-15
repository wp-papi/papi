<?php

/**
 * Admin class that handles admin tabs in
 * meta boxes.
 */
final class Papi_Admin_Meta_Box_Tabs {

	/**
	 * The tabs.
	 *
	 * @var array
	 */
	private $tabs = [];

	/**
	 * The constructor.
	 *
	 * @param array $tabs
	 * @param bool  $render
	 */
	public function __construct( array $tabs = [], $render = true ) {
		if ( empty( $tabs ) ) {
			return;
		}

		$this->tabs = papi_tabs_setup( $tabs );

		if ( $render ) {
			$this->html();
		}
	}

	/**
	 * Get the tabs that are registered.
	 *
	 * @return array
	 */
	public function get_tabs() {
		return $this->tabs;
	}

	/**
	 * Generate html for tabs and properties.
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
					<li class="<?php echo $this->tabs[0] === $tab ? 'active' : ''; ?>">
						<a href="#" data-papi-tab="<?php echo $tab->id; ?>">
							<?php if ( ! empty( $tab->icon ) ): ?>
								<img src="<?php echo $tab->icon; ?>" alt="<?php echo $tab->title; ?>"/>
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
					<div class="<?php echo $this->tabs[0] === $tab ? 'active' : ''; ?>"
					     data-papi-tab="<?php echo $tab->id; ?>">
						<?php
						$properties = array_map( function ( $property ) {
							// While in a tab the sidebar is required.
							$property->sidebar = true;

							return $property;
						}, $tab->properties );

						papi_render_properties( $properties );
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
