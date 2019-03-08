<?php

/**
 * Core class that handle if a conditional rule
 * should display the property or not.
 */
class Papi_Core_Conditional {

	/**
	 * Available relations.
	 *
	 * @var array
	 */
	protected $relations = [
		'AND',
		'OR'
	];

	/**
	 * Check if the property should be displayed by the rules.
	 *
	 * @param  array $rules
	 * @param  Papi_Core_Property $property
	 *
	 * @return bool
	 */
	public function display( array $rules, $property = null ) {
		if ( empty( $rules ) ) {
			return true;
		}

		$rules  = $this->prepare_rules( $rules, $property );

		if ( in_array( $rules['relation'], $this->relations, true ) ) {
			return $this->display_by_relation( $rules );
		}

		return true;
	}

	/**
	 * Get the display by relation.
	 *
	 * @param  array $rules
	 *
	 * @return bool
	 */
	protected function display_by_relation( array $rules ) {
		if ( $rules['relation'] === 'AND' ) {
			$display = true;

			foreach ( $rules as $rule ) {
				if ( ! $display ) {
					break;
				}

				if ( papi_is_rule( $rule ) ) {
					/**
					 * Modify rule allowed.
					 *
					 * @param  bool $result
					 * @param  Papi_Core_Conditional_Rule $rule
					 *
					 * @return bool
					 */
					$display = apply_filters( 'papi/conditional/rule_allowed', papi_filter_conditional_rule_allowed( $rule ), $rule );
				}
			}

			return $display;
		}

		$empty = array_filter( $rules, function ( $rule ) {
			return papi_is_rule( $rule ) ? true : null;
		} );

		if ( empty( $empty ) ) {
			return true;
		}

		$result = [];

		foreach ( $rules as $rule ) {
			if ( papi_is_rule( $rule ) ) {
				/**
				 * Modify rule allowed.
				 *
				 * @param  bool $result
				 * @param  Papi_Core_Conditional_Rule $rule
				 *
				 * @return bool
				 */
				$result[] = apply_filters( 'papi/conditional/rule_allowed', papi_filter_conditional_rule_allowed( $rule ), $rule );
			}
		}

		$result = array_filter( $result, function ( $res ) {
			return $res === true ? true : null;
		} );

		return ! empty( $result );
	}

	/**
	 * Get rule slug.
	 *
	 * @param  Papi_Core_Conditional_Rule $rule
	 * @param  Papi_Core_Property $property
	 *
	 * @return string
	 */
	protected function get_rule_slug( $rule, $property ) {
		$arr_reg = '/\[\d+\](\[\w+\])$/';
		$slug    = $property->get_slug();

		$page_type = papi_get_entry_type_by_meta_id();

		if ( $page_type instanceof Papi_Page_Type === false ) {
			return $rule->slug;
		}

		if ( preg_match( $arr_reg, $slug, $out ) ) {
			$slug     = str_replace( $out[1], '[' . unpapify( $rule->slug ) . ']', $slug );
			$property = $page_type->get_property( $slug );

			if ( papi_is_property( $property ) ) {
				return $slug;
			}
		}

		return $rule->slug;
	}

	/**
	 * Prepare rules.
	 *
	 * @param  array $rules
	 * @param  Papi_Core_Property $property
	 *
	 * @return array
	 */
	public function prepare_rules( array $rules, $property = null ) {
		if ( ! isset( $rules['relation'] ) ) {
			$rules['relation'] = 'OR';
		} else {
			$rules['relation'] = strtoupper( $rules['relation'] );
		}

		foreach ( $rules as $index => $value ) {
			if ( is_string( $index ) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$rules[$index] = new Papi_Core_Conditional_Rule( $value );

				if ( strpos( $rules[$index]->slug, '.' ) === false && papi_is_property( $property ) ) {
					$rules[$index]->slug = $this->get_rule_slug(
						$rules[$index],
						$property
					);
				}
			}
		}

		return $rules;
	}
}
