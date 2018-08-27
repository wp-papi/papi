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
	protected $tabs = [];

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
	protected function html() {
		?>
		<div class="papi-tabs-wrapper">
			<div class="papi-tabs-table-back"></div>
			<div class="papi-tabs-back"></div>
			<ul class="papi-tabs">
				<?php
				foreach ( $this->tabs as $tab ):
					$css_classes = $this->tabs[0] === $tab ? 'active ' : '';

					if ( empty( $tab->background ) ) {
						// Find out if the first property has a sidebar or not. If the first property
						// don't have a sidebar the tab background should be white since it looks better.
						$no_sidebar = empty( $tab->properties ) ? false : $tab->properties[0]->sidebar;
						$css_classes .= ! empty( $tab->properties ) && $no_sidebar ? '' : 'white-tab';
					} else {
						$css_classes .= $tab->background === 'white' ? 'white-tab' : '';
					}
					?>
					<li class="<?php echo esc_attr( $css_classes ); ?>">
						<a href="#" data-papi-tab="<?php echo esc_attr( $tab->id ); ?>">
							<?php if ( ! empty( $tab->icon ) ): ?>
								<img src="<?php echo esc_attr( $tab->icon ); ?>" alt="<?php echo esc_attr( $tab->title ); ?>" />
							<?php endif;
							echo esc_html( $tab->title ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="papi-tabs-content">
				<?php
				foreach ( $this->tabs as $tab ):
					?>
					<div class="<?php echo $this->tabs[0] === $tab ? 'active' : ''; ?>" data-papi-tab="<?php echo esc_attr( $tab->id ); ?>">
						<?php papi_render_properties( $tab->properties ); ?>
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
