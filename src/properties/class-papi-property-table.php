<?php

class Papi_Property_Table extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'array';

	/**
	 * Build table from array data.
	 *
	 * @param  array $arr
	 * @param  bool  $child
	 *
	 * @return stringe
	 */
	protected function build_table( array $arr, $child = false ) {
		$html = '<div class="papi-property-table"><table class="papi-table">';
		$allow_html = $this->get_setting( 'allow_html' );

		if ( $child ) {
			$html .= '<thead>';

			foreach ( $arr[0] as $key => $value ) {
				if ( $allow_html ) {
					$key = html_entity_decode( $key );
				}

				$html .= sprintf( '<th>%s</th>', $key );
			}

			$html .= '</thead>';
		}

		foreach ( $arr as $key => $value ) {
			$html .= '<tr>';

			foreach ( $value as $key2 => $value2 ) {
				if ( is_array( $value2 ) ) {
					$value2 = $this->build_table( $value2, true );
				}

				$value2 = papi_convert_to_string( $value2 );

				if ( $allow_html ) {
					$value2 = html_entity_decode( $value2 );
				}

				$html .= sprintf( '<td>%s</td>', $value2 );
			}

			$html .= '</tr>';
		}

		return $html . '</table></div>';
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'allow_html'  => false,
			'items'       => [],
		];
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$value = $this->get_value();
		$data  = $this->get_setting( 'items' );

		if ( ! is_array( $data ) ) {
			return;
		}

		// Convert key/value array to [key, value] value.
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				continue;
			}

			$data[$key] = [$key, $value];
		}

		echo $this->build_table( $data ); // wpcs: xss ok
	}
}
