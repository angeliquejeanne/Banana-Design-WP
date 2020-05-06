<?php

/**
 * Class Hustle_Module_Collection
 *
 *
 */
class Hustle_Module_Collection extends Hustle_Collection {

	/**
	 * @return Hustle_Module_Collection
	 */
	public static function instance(){
		return new self();
	}

	/**
	 * Returns array of Hustle_Module_Model
	 *
	 *
	 * @param bool|true $active
	 * @param array $args
	 * @param int $limit
	 * @return array Hustle_Module_Model[]
	 */
	public function get_all( $active = true, $args = array(), $limit = -1 ) {
		/**
		 * types
		 */
		$types = ( isset( $args['module_type'] ) ) ? array( $args['module_type'] ): array() ;
		if (
			empty( $types )
			&& isset( $args['filter'] )
			&& isset( $args['filter']['types'] )
			&& ! empty( $args['filter']['types'] )
		) {
			$types = $args['filter']['types'];
		}
		/**
		 * set offset
		 */
		$offset = '';
		if ( 0 < $limit && isset( $args['page'] ) && 0 < $args['page'] ) {
			$offset = self::$_db->prepare( 'OFFSET %d ', ( $args['page'] - 1 ) * $limit );
		}
		/**
		 * set limit
		 */
		$limit = -1 !== $limit? self::$_db->prepare( 'LIMIT %d ', $limit ) : '';
		/**
		 * Conditions
		 */
		$module_type_condition = '';
		if ( is_array( $types ) && ! empty( $types ) ) {
			$v = implode( ',', array_map( array( $this, '_wrap_string' ), $types ) );
			$module_type_condition .= 'AND m.`module_type` IN ( ' . $v .' ) ';
		}
		$module_type_condition .= ( isset($args['except_types']) ) ? $this->prepare_except_module_types_condition( $args['except_types'] ) : "";
		/**
		 * join
		 */
		$join = '';
		if (
			isset( $args['meta'] )
			&& isset( $args['meta']['key'] )
			&& isset( $args['meta']['value'] )
		) {
			switch ( $args['meta']['value'] ) {
				/**
				 * handle "NOT EXISTS" option.
				 */
			case 'NOT EXISTS':
				$join .= 'LEFT JOIN '.Hustle_Db::modules_meta_table().' AS cf ON cf.`module_id` = m.`module_id` ';
				$join .= self::$_db->prepare(
					'AND cf.`meta_key` = %s ',
					$args['meta']['key']
				);
				$module_type_condition .= 'AND cf.`meta_value` IS NULL ';
				break;
			default:
				$join .= 'JOIN '.Hustle_Db::modules_meta_table().' AS cf ON cf.`module_id` = m.`module_id` ';
				$join .= self::$_db->prepare(
					'AND cf.`meta_key` = %s AND cf.`meta_value` = %s ',
					$args['meta']['key'],
					$args['meta']['value']
				);
			}
		}

		// Get filter by 'edit_role'.
		if (
			isset( $args['filter'] )
			&& isset( $args['filter']['role'] )
			&& 'any' !== $args['filter']['role']
		) {
			$filter_role = $args['filter']['role'];
		}

		//filter modules by edit_roles
		if ( !empty( $filter_role ) ) {
			$join .= 'JOIN '.Hustle_Db::modules_meta_table().' AS cf1 ON cf1.`module_id` = m.`module_id` AND cf1.`meta_key` = "edit_roles" ';
			$join .= self::$_db->prepare(
				'AND ( cf1.`meta_value` LIKE %s ) ',
				'%"' . $filter_role . '"%'
			);
		}

		// Get filter by 'can_edit'.
		if ( isset( $args['filter']['can_edit'] ) && true === $args['filter']['can_edit'] &&  ! current_user_can( 'hustle_create' ) ) {

			$user = wp_get_current_user();
			$current_user_roles = (array) $user->roles;

			$join .= ' JOIN '.Hustle_Db::modules_meta_table().' AS cf1 ON cf1.`module_id` = m.`module_id` AND cf1.`meta_key` = "edit_roles" AND (1=0';
			foreach ( $current_user_roles as $role ) {
				$join .= self::$_db->prepare(
					' OR cf1.`meta_value` LIKE %s',
					'%"' . $role . '"%'
				);
			}
			$join .= ')';

		}

		/**
		 * search
		 */
		if (
			isset( $args['filter'] )
			&& isset( $args['filter']['q'] )
			&& ! empty( $args['filter']['q'] )
		) {
			$module_type_condition .= self::$_db->prepare( 'AND m.`module_name` LIKE %s ', '%' . $args['filter']['q'] . '%' );
		}
		/**
		 * build query
		 */
		$query = 'SELECT ';
		/**
		 * return count only
		 */
		if ( isset( $args['count_only'] ) && $args['count_only'] ) {
			$limit = '';
			$offset = '';
			$query .= 'COUNT( distinct m.`module_id` )';
		} else {
			$query .= 'm.`module_id` ';
		}
		$query .= 'FROM ' . Hustle_Db::modules_table() . ' AS m '.$join.'WHERE 1 ';

		/**
		 * Add blog_id for multisite main site, to avoid getting modules from
		 * another sites - it is only used before migration is done.
		 */
		$is_multiste = is_multisite();
		if ( $is_multiste ) {
			$main_id = get_main_site_id();
			$current_blog_id = get_current_blog_id();
			if ( $main_id === $current_blog_id ) {
				$query .= self::$_db->prepare(
					'AND m.`blog_id` IN ( 0, %d ) ',
					$main_id
				);
			}
		}

		if( 'any' !== $active && ! is_null( $active ) ) {
			$query .= self::$_db->prepare( "AND m.`active`= %d ", (int) $active );
		}

		// Module mode.
		$module_mode = isset( $args['module_mode'] ) ? $args['module_mode'] : '';
		if( ! empty( $module_mode ) ) {
			$query .= self::$_db->prepare( " AND m.`module_mode`= %s ", $module_mode );
		}
		$query .= $module_type_condition .' ';
		/**
		 * Order
		 */
		if ( !isset( $args['count_only'] ) || !$args['count_only'] ) {
			$query .= 'ORDER BY ';
			if (
				isset( $args['filter'] )
				&& isset( $args['filter']['sort'] )
				&& ! empty( $args['filter']['sort'] )
				&& 'module_name' !== $args['filter']['sort']
			) {
				//$query .= sprintf( 'm.`%s`, ', $args['filter']['sort'] );
				$query .= self::$_db->prepare( 'm.%s, ', $args['filter']['sort'] );
			}
			$query .= 'm.`module_name` ';
		}
		$query .= $limit.' '.$offset;
		/**
		 * return count only
		 */
		if ( isset( $args['count_only'] ) && $args['count_only'] ) {
			return self::$_db->get_var( $query );
		}

		$ids = self::$_db->get_col( $query );
		/**
		 * check is empty?
		 */
		if ( empty( $ids ) ) {
			return $ids;
		}
		/**
		 * Return only ids if it is needed
		 */
		if ( isset( $args['fields'] ) && 'ids' === $args['fields'] ) {
			return $ids;
		}
		return array_map( array( $this, 'return_model_from_id' ), $ids );
	}

	/**
	 * Helper for get_all() with pagination and filters.
	 *
	 * @since 4.0.0
	 */
	public function get_all_paginated( $args = array() ) {
		$limit = apply_filters( 'hustle_module_collection_page_size', 10 );
		$page = intval( filter_input( INPUT_GET, 'paged', FILTER_VALIDATE_INT ) );
		$filters = $this->get_filters();
		$count = $this->get_all( null, array(
			'count_only' => true,
			'filter' => $filters
		) );
		$modules = $this->get_all( null, array(
			'page' => $page,
			'filter' => $filters,
		), $limit );

		$results = array(
			'count' => $count,
			'page' => $page,
			'limit' => $limit,
			'modules' => $modules,
			'show_pager' => $count > $limit,
			'filter' => $filters,
		);
		return $results;
	}

	public function prepare_except_module_types_condition( $excepts ) {
		$except_condition = "";
		foreach( $excepts as $except ) {
			$except_condition .= " AND `module_type` != '". $except ."'";
		}
		return $except_condition;
	}

	/**
	 * TODO: no need for this to be a method of this class. Make it a regular function instead.
	 */
	public function return_model_from_id( $id ) {

		$error = new WP_Error( 'not_found', __( 'Module not found.', 'hustle' ) );
		if ( empty( $id ) ) {
			return $error;
		}

		$module = Hustle_Module_Model::instance();
		$module_type = $module->get_module_type_by_module_id( $id );

		if ( 'social_sharing' === $module_type ) {
			$model_instance = Hustle_SShare_Model::instance()->get( $id );
		} else {
			$model_instance = $module->get( $id );
		}

		if ( ! is_wp_error( $model_instance ) ) {
			return $model_instance;
		}

		return $error;
	}

	public function get_all_id_names(){
		return self::$_db->get_results( self::$_db->prepare( "SELECT `module_id`, `module_name` FROM " . Hustle_Db::modules_table() ." WHERE `active`=%d", 1 ), OBJECT );
	}

	/**
	 * Includes Embed and Social Sharing module
	*/
	public function get_embed_id_names( $module_types = array() ) {
		$types = '';
		if ( !empty($module_types) ) {
			$temp_array = array();
			foreach( $module_types as $type ) {
				array_push( $temp_array, '`module_type` = "'. $type .'"' );
			}
			$types = ' AND ( '. implode( ' OR ', $temp_array ) . ' )';
		}
		return self::$_db->get_results( self::$_db->prepare( "SELECT `module_id`, `module_name` FROM " . Hustle_Db::modules_table() ." WHERE `active`=%d" . $types, 1 ), OBJECT );
	}

	public function get_hustle_20_page_shares() {
		$page_shares = self::$_db->get_results( self::$_db->prepare( "SELECT optin_id, meta_key, meta_value FROM `" . self::$_db->base_prefix . "optin_meta` WHERE meta_key like '%s'", '%_page_shares' ) );
		return $page_shares;
	}

	// TODO: fix this. This might timeout in sites with a lot of data or small limits.
	public function get_hustle_30_modules( $blog_id = null ) {
		$db = self::$_db;
		$sql = $db->prepare(
			"SELECT * FROM `". $db->base_prefix ."hustle_modules` WHERE `blog_id` > 0 AND `blog_id` = %d",
			get_current_blog_id()
		);
		$modules_result = $db->get_results( $sql );
		$prepared_array = array(
			'popup_view',
			'popup_conversion',
			'slidein_view',
			'slidein_conversion',
			'after_content_view',
			'shortcode_view',
			'floating_social_view',
			'floating_social_conversion',
			'widget_view',
			'after_content_conversion',
			'shortcode_conversion',
			'widget_conversion',
			'subscription'
		);
		$meta_keys_placeholders = implode( ', ', array_fill( 0, count( $prepared_array ), '%s' ) );
		$modules = array();
		foreach( $modules_result as $row ) {
			$module_id = $row->module_id;
			$modules[ $module_id ] = $row;
			// Getting the modules with the regular methods shouldn't work in MU
			// because we use $db->prefix in 4.0, instead of $db->base_prefix as in 3.x.
			$sql = $db->prepare(
				"SELECT `meta_value`, `meta_key`
				FROM `{$db->base_prefix}hustle_modules_meta`
				WHERE `module_id` = %d",
				$module_id );
			$sql .= $db->prepare( " AND `meta_key` NOT IN ({$meta_keys_placeholders})", $prepared_array );
			$meta_result = $db->get_results( $sql );
			$meta = array();
			foreach( $meta_result as $row ) {
				if ( 'shortcode_id' !== $row->meta_key ) {
					$meta[ $row->meta_key ] = json_decode( $row->meta_value, true );
				} else {
					$meta[ $row->meta_key ] = $row->meta_value;
				}
			}
			$modules[ $module_id ]->meta = $meta;
		}

		return $modules;

	}

	/**
	 * Get the id of the modules that belong to a blog.
	 * Used to migrate tracking data.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_30_modules_ids_by_blog( $blog_id ) {
		$modules_table = self::$_db->base_prefix . Hustle_Db::TABLE_HUSTLE_MODULES;
		return self::$_db->get_col( self::$_db->prepare( "SELECT `module_id` FROM " . $modules_table ." WHERE `blog_id`=%d ORDER BY `module_id` ASC", $blog_id ) );
	}

	/**
	 * Helper for filters
	 *
	 * @since 4.0.0
	 */
	private function get_filters() {
		$filters = isset( $_REQUEST['filter'] )? $_REQUEST['filter'] : array(); // WPCS: CSRF ok.
		if ( isset( $filters['types'] ) && is_string( $filters['types'] ) ) {
			$filters['types'] = explode(',', $filters['types'] );
		}
		$defaults = array(
			'types' => array(),
			'q' => '',
			'role' => 'any',
			'sort' => 'module_name'
		);
		$filters = wp_parse_args( $filters, $defaults );
		return $filters;
	}

	/**
	 * Get active providers on modules
	 *
	 * @since 4.0.1
	 */
	public static function get_active_providers_module( $slug ) {
		global $wpdb;
		$modules_meta_table = Hustle_Db::modules_meta_table();

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$query = $wpdb->prepare(
			"SELECT `module_id`
			FROM {$modules_meta_table}
			WHERE `meta_value`
			LIKE %s
			AND `meta_key` = 'integrations_settings'",
			"%". $slug . "%"
		);
		return $wpdb->get_col( $query );
		// phpcs:enable
	}

}
