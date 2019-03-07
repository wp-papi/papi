<?php

class Papi_Property_Pure extends Papi_Property {

	/**
	 * Render property.
	 */
    public function html() {
		if ( is_callable( $this->options->render ) ) {
			echo call_user_func( $this->options->render, $this->options );
			return;
		}

		echo apply_filters( 'papi/property/' . $this->options->type, $this->options );
	}
}

function papi_register_property($name, $callback) {
}
