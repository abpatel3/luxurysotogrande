<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the Emails
 *
 */
Class WPBS_Object_DB_Emails extends WPBS_Object_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'wpbs_emails';
		$this->primary_key 		 = 'id';
		$this->context 	  		 = 'email';
		$this->query_object_type = 'WPBS_Email';

	}


	/**
	 * Return the table columns 
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		    => '%d',
			'form_id' 	    => '%d',
			'name' 		    => '%s',
			'is_enabled'    => '%d',
			'date_created' 	=> '%s',
			'date_modified' => '%s',
			'status'		=> '%s',
		);

	}


	/**
	 * Returns an array of WPBS_Email objects from the database
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_emails( $args = array(), $count = false ) {

		$defaults = array(
			'form_id'   => 0,
			'number'    => -1,
			'offset'    => 0,
			'orderby'   => 'id',
			'is_enabled'   => [0,1],
			'order'     => 'DESC',
			'include'   => array(),
			'search'	=> ''
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the query arguments just before making the db call
		 *
		 * @param array $args
		 *
		 */
		$args = apply_filters( 'wpbs_get_emails_args', $args );

		// Number args
		if( $args['number'] < 1 )
			$args['number'] = 999999;

		// Where clause
		$where = "WHERE 1=1";

		// Status where clause
		if( ! empty( $args['status'] ) ) {

			$status = esc_sql( $args['status'] );
			$where .= " AND status = '{$status}'";

		}

		// Status where clause
		if( ! empty( $args['form_id'] ) ) {

			$form_id = esc_sql( $args['form_id'] );
			$where .= " AND form_id = '{$form_id}'";

		}

		// active where clause
        if (is_array($args['is_enabled'])) {

            $is_enabled = implode(',',$args['is_enabled']);
            $where .= " AND is_enabled IN ({$is_enabled})";

        }

		
		// Include where clause
		if( ! empty( $args['include'] ) ) {

			$include = implode( ',', $args['include'] );
			$where  .= " AND id IN({$include})";

		}

		// Include search
		if( ! empty( $args['search'] ) ) {

			$search = esc_sql( $args['search'] );
			$where  .= " AND name LIKE '%%{$search}%%'";

		}

		// Orderby
		$orderby = in_array($args['orderby'], array('id', 'name', 'date_created', 'date_modified', 'status')) ? esc_sql($args['orderby']) : 'id';

		// Order
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		$clauses = compact( 'where', 'orderby', 'order', 'count' );

		$results = $this->get_results( $clauses, $args, 'wpbs_get_email' );

		return $results;

	}


	/**
	 * Creates and updates the database table for the emails
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			id bigint(10) NOT NULL AUTO_INCREMENT,
			form_id bigint(10) NOT NULL,
			name text NOT NULL,
			is_enabled tinyint(1) NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			status text NOT NULL,
			PRIMARY KEY (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}