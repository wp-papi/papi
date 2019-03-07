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

		if ( $child ) {
			$html .= '<thead>';

			foreach ( $arr[0] as $key => $value ) {
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

				$html .= sprintf( '<td>%s</td>', papi_convert_to_string( $value2 ) );
			}

			$html .= '</tr>';
		}

		return $html . '</table></div>';
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

		echo $this->build_table( $data );
	}
}
