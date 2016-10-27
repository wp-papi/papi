<?php

class Papi_Query {

	/**
	 * The query arguments.
	 *
	 * @var array
	 */
	protected $args;

	/**
	 * The default query arguments.
	 *
	 * @var array
	 */
	protected $default_args = [
		'entry_type' => ''
	];

	/**
	 * The query type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The query instance.
	 *
	 * @var WP_Term_Query|WP_Query
	 */
	protected $query;

	/**
	 * Query constructor.
	 *
	 * @param array  $args
	 * @param string $type
	 */
	public function __construct( array $args = [], $type = 'post' ) {
		$this->type  = $type === 'page' ? 'post' : $type;
		$this->query = $this->get_query_class();
		$this->args  = $args;
	}

	/**
	 * Dynamically access query properties.
	 *
	 * @param  string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'posts':
			case 'terms':
				return $this->get_result();
			default:
				break;
		}
	}

	/**
	 * Get first item of result.
	 *
	 * @return array
	 */
	public function first() {
		$result = $this->get_result();

		return array_shift( $result );
	}

	/**
	 * Parse query arguments.
	 *
	 * @param  array $args
	 */
	public function parse_args( array $args ) {
		$args = array_merge( $this->default_args, $args );

		// Since a page type has defined post types we should use them.
		// With a fallback on `post_type` args or `any` value.
		if ( $this->type === 'post' ) {
			$args = $this->parse_post_args( $args );
		} else if ( $this->type === 'term' ) {
			$args = $this->parse_term_args( $args );
		}

		$this->args = $args;
	}

	/**
	 * Parse post query arguments.
	 *
	 * @param  array $args
	 *
	 * @return array
	 */
	protected function parse_post_args( array $args ) {
		if ( isset( $args['page_type'] ) ) {
			$args['entry_type'] = $args['page_type'];

			unset( $args['page_type'] );
		}

		$entry_type = papi_get_entry_type_by_id( $args['entry_type'] );

		if ( $entry_type instanceof Papi_Page_Type ) {
			$args['post_type'] = papi_to_array( $entry_type->post_type );
		} else {
			$args['post_type'] = isset( $args['post_type'] ) ? $args['post_type'] : '';
		}

		return $args;
	}

	/**
	 * Parse term query arguments.
	 *
	 * @param  array $args
	 *
	 * @return array
	 */
	protected function parse_term_args( array $args ) {
		if ( isset( $args['taxonomy_type'] ) ) {
			$args['entry_type'] = $args['taxonomy_type'];

			unset( $args['taxonomy_type'] );
		}

		$entry_type = papi_get_entry_type_by_id( $args['entry_type'] );

		if ( $entry_type instanceof Papi_Taxonomy_Type ) {
			$args['taxonomy'] = papi_to_array( $entry_type->taxonomy );
		} else {
			$args['taxonomy'] = isset( $args['taxonomy'] ) ? $args['taxonomy'] : '';
		}

		return $args;
	}

	/**
	 * Get query object for right query type.
	 *
	 * @return WP_Query|WP_Term_Query
	 */
	public function get_query_class() {
		switch ( $this->type ) {
			case 'post':
			case 'page':
				return new WP_Query;
			case 'term':
				// `WP_Term_Query` was added in WordPress 4.6.
				if ( class_exists( 'WP_Term_Query' ) ) {
					return new WP_Term_Query;
				}

				break;
			default:
				break;
		}
	}

	/**
	 * Get real query arguments without Papi Query specific arguments.
	 *
	 * @return array
	 */
	public function get_query_args() {
		$args = $this->args;

		if ( empty( $args['meta_query'] ) ) {
			// Add new meta key/value if `meta_key` or `meta_value` is empty.
			if ( empty( $args['meta_key'] ) || empty( $args['meta_value'] ) ) {
				$args['meta_key']   = papi_get_page_type_key();
				$args['meta_value'] = $args['entry_type'];
			} else if ( papi_entry_type_exists( $args['entry_type'] ) ) {
				$item = [
					'key'   => $args['meta_key'],
					'value' => $args['meta_value']
				];

				// Add `meta_compare` if set.
				if ( isset( $args['meta_compare'] ) ) {
					$item['compare'] = $args['meta_compare'];

					unset( $args['meta_compare'] );
				}

				// Add new meta query item.
				$args['meta_query'][] = $item;

				// Add Papi entry/page type meta query.
				$args['meta_query'][] = [
					'key'   => papi_get_page_type_key(),
					'value' => $args['entry_type']
				];

				// Add meta query relation when two query items.
				if ( isset( $args['relation'] ) ) {
					$args['meta_query']['relation'] = $args['relation'];
				} else {
					$args['meta_query']['relation'] = 'AND';
				}

				unset( $args['meta_key'] );
				unset( $args['meta_value'] );
			}
		} else if ( papi_entry_type_exists( $args['entry_type'] ) ) {
			// Add Papi entry/page type meta query.
			$args['meta_query'][] = [
				'key'   => papi_get_page_type_key(),
				'value' => $args['entry_type']
			];

			// Add meta query relation if not set.
			if ( ! isset( $args['meta_query']['relation'] ) ) {
				$args['meta_query']['relation'] = 'AND';
			}
		}

		// Since the real query classes don't support
		// custom arguments the should be deleted.
		foreach ( array_keys( $this->default_args ) as $key ) {
			if ( isset( $args[$key] ) ) {
				unset( $args[$key] );
			}
		}

		return $args;
	}

	/**
	 * Get result.
	 *
	 * Works for all query types.
	 *
	 * @return array
	 */
	public function get_result() {
		if ( ! method_exists( $this->query, 'query' ) ) {
			return [];
		}

		$this->parse_args( $this->args );

		return $this->query->query( $this->get_query_args() );
	}

	/**
	 * Get last item of result.
	 *
	 * @return array
	 */
	public function last() {
		$result = $this->get_result();

		return array_pop( $result );
	}
}
