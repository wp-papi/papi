<?php

class Papi_Core_Data_Table {

	/**
	 * Core data table constructor.
	 */
	public function __construct() {
		$this->create_table();
	}

	/**
	 * Delete item from database.
	 *
	 * @param  integer $object_id
	 * @param  string  $object_type
	 * @param  integer $site_id
	 *
	 * @return bool
	 */
	public function delete( int $object_id, string $object_type, int $site_id = 0 ) {
		global $wpdb;

		return (bool) $wpdb->delete( $this->get_table(), [
			'object_id'  => $object_id,
			'meta_key'   => $key,
		], ['%d', '%s'] );
	}

	/**
	 * Create sync id by saving post id to database.
	 *
	 * @param  integer $object_id
	 * @param  string  $object_type
	 * @param  integer $sync_id
	 * @param  integer $site_id
	 *
	 * @return bool|integer
	 */
	public function update( $object_id, $key, $value ) {
		global $wpdb;

		return $wpdb->insert( $this->get_table(), [
			'object_id'  => $object_id,
			'meta_key'   => $key,
			'meta_value' => maybe_serialize( $value ),
		], ['%d', '%s', '%s'] );
	}

	/**
	 * Get value from database based on key.
	 *
	 * @param  integer $object_id
	 * @param  string  $object_type
	 * @param  string  $key
	 *
	 * @return integer
	 */
	public function get( $object_id, $key ) {
		global $wpdb;

		$value = $wpdb->get_results( $wpdb->prepare( // wpcs: unprepared SQL
			"SELECT {$key} FROM `{$this->get_table()}` WHERE object_id = %d", // wpcs: unprepared SQL
			$object_id,
		) );


		if ( empty( $value ) ) {
			return '';
		}

		return isset( $value[0]->meta_value ) ? intval( $value[0]->meta_value ) : '';
	}

	/**
	 * Get table name.
	 *
	 * @return mixed
	 */
	protected function get_table() {
		global $wpdb;

		return sprintf( '%spapimeta', $wpdb->prefix );
	}

	/**
	 * Create table if missing or not same version.
	 */
	protected function create_table() {
		if ( ! function_exists( 'get_site_option' ) ) {
			return;
		}

		$table_version     = 1;
		$installed_version = intval( get_site_option( '_papi_core_data_table_version', 0 ) );

		if ( $installed_version !== $table_version ) {
			global $wpdb;

			$wpdb->query( "DROP TABLE IF EXISTS `{$this->get_table()}`" ); // wpcs: unprepared SQL

			$sql = sprintf(
				'CREATE TABLE %1$s (
					meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					object_id bigint(20) unsigned NOT NULL,
					meta_key varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
					meta_value longtext COLLATE utf8mb4_unicode_520_ci,
					PRIMARY KEY (meta_id),
					KEY object_id (object_id),
					KEY meta_key (meta_key(191))
				  ) %2$s;',
				$this->get_table(),
				$GLOBALS['wpdb']->get_charset_collate()
			);

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			update_site_option( '_papi_core_data_table_version', $table_version );
		}
	}
}
