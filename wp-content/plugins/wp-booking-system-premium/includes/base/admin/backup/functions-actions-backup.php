<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Handles the export of the backup file
 *
 */
function wpbs_action_backup_export() {

	// Let only admins do this procedure
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Verify nonce
	$token = isset( $_POST['wpbs_token'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbs_token'] ) ) : '';

	if ( empty( $token ) || ! wp_verify_nonce( $token, 'wpbs_backup_export' ) ) {
		return;
	}

	global $wpdb;

	// Prevent timeout / allow long export
	@set_time_limit( 0 );
	@ignore_user_abort( true );

	if ( function_exists( 'gc_enable' ) ) {
		gc_enable();
	}

	// Clear output buffers so we can stream the file
	while ( ob_get_level() ) {
		@ob_end_clean();
	}

	nocache_headers();

	header( 'Content-Disposition: attachment; filename=wpbs-export-' . current_time( 'Y-m-d-H-i-s' ) . '.wpbs' );
	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
	header( 'X-Accel-Buffering: no' ); // Helpful on some servers/proxies

	$batch_size = (int) apply_filters( 'wpbs_backup_export_batch_size', 250 );

	if ( $batch_size < 1 ) {
		$batch_size = 250;
	}

	echo '{';

	$first_top_level_key = true;

	/**
	 * Export plugin tables in batches
	 */
	foreach ( wp_booking_system()->db as $table_slug => $db_object ) {

		if ( ! $first_top_level_key ) {
			echo ',';
		}
		$first_top_level_key = false;

		echo wp_json_encode( (string) $table_slug ) . ':[';

		$table_name = str_replace( '`', '``', $db_object->table_name );
		$offset     = 0;
		$first_row  = true;

		do {
			$query = $wpdb->prepare(
				"SELECT * FROM `{$table_name}` LIMIT %d OFFSET %d",
				$batch_size,
				$offset
			);

			$rows = $wpdb->get_results( $query, ARRAY_A );

			if ( empty( $rows ) ) {
				break;
			}

			foreach ( $rows as $row ) {
				if ( ! $first_row ) {
					echo ',';
				}
				$first_row = false;

				echo wp_json_encode( $row );
			}

			$offset += $batch_size;

			unset( $rows );

			if ( function_exists( 'gc_collect_cycles' ) ) {
				gc_collect_cycles();
			}

			@flush();

			if ( connection_aborted() ) {
				exit;
			}

		} while ( true );

		echo ']';
	}

	/**
	 * General settings and plugin version number
	 */
	echo ',' . wp_json_encode( 'settings' ) . ':' . wp_json_encode( get_option( 'wpbs_settings', array() ) );
	echo ',' . wp_json_encode( 'plugin_version' ) . ':' . wp_json_encode( WPBS_VERSION );

	echo '}';
	exit;
}
add_action( 'wpbs_action_backup_export', 'wpbs_action_backup_export' );


/**
 * Handles the export of the backup file
 *
 */
function wpbs_action_backup_import() {

	// Let only admins do this procedure
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Verify nonce
	$token = isset( $_POST['wpbs_token'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbs_token'] ) ) : '';

	if ( empty( $token ) || ! wp_verify_nonce( $token, 'wpbs_backup_import' ) ) {
		return;
	}

	// Check if file has been selected
	if ( empty( $_FILES['import_file']['tmp_name'] ) ) {

		wpbs_admin_notices()->register_notice( 'import_file_missing', '<p>' . __( 'Please select a file.', 'wp-booking-system' ) . '</p>', 'error' );
		wpbs_admin_notices()->display_notice( 'import_file_missing' );

		return;
	}

	$file_name = isset( $_FILES['import_file']['name'] ) ? wp_unslash( $_FILES['import_file']['name'] ) : '';
	$file_tmp  = isset( $_FILES['import_file']['tmp_name'] ) ? $_FILES['import_file']['tmp_name'] : '';

	// Check for proper extension
	if ( 'wpbs' !== strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) ) ) {

		wpbs_admin_notices()->register_notice( 'import_file_extension_fail', '<p>' . __( 'The file you are trying to import does not have a valid extension. The extension of the file should be ".wpbs".', 'wp-booking-system' ) . '</p>', 'error' );
		wpbs_admin_notices()->display_notice( 'import_file_extension_fail' );

		return;
	}

	if ( ! is_readable( $file_tmp ) ) {

		wpbs_admin_notices()->register_notice( 'import_file_read_fail', '<p>' . __( 'The selected file could not be read.', 'wp-booking-system' ) . '</p>', 'error' );
		wpbs_admin_notices()->display_notice( 'import_file_read_fail' );

		return;
	}

	@set_time_limit( 0 );
	@ignore_user_abort( true );

	if ( function_exists( 'gc_enable' ) ) {
		gc_enable();
	}

	/**
	 * First pass:
	 * Validate file structure and make sure plugin_version exists
	 * BEFORE truncating tables.
	 */
	$validation = wpbs_backup_stream_validate_file( $file_tmp );

	if ( is_wp_error( $validation ) ) {

		wpbs_admin_notices()->register_notice( 'import_file_invalid', '<p>' . esc_html( $validation->get_error_message() ) . '</p>', 'error' );
		wpbs_admin_notices()->display_notice( 'import_file_invalid' );

		return;
	}

	/**
	 * Second pass:
	 * Truncate and import progressively.
	 */
	$import = wpbs_backup_stream_import_file( $file_tmp );

	if ( is_wp_error( $import ) ) {

		wpbs_admin_notices()->register_notice( 'import_file_process_fail', '<p>' . esc_html( $import->get_error_message() ) . '</p>', 'error' );
		wpbs_admin_notices()->display_notice( 'import_file_process_fail' );

		return;
	}

	// Redirect with a success message
	wp_redirect(
		add_query_arg(
			array(
				'page'         => 'wpbs-backup',
				'wpbs_message' => 'import_file_success',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'wpbs_action_backup_import', 'wpbs_action_backup_import' );


/**
 * Validates the backup file without loading it all into memory.
 *
 * We only scan the top-level JSON keys and make sure plugin_version exists.
 * 
 */
function wpbs_backup_stream_validate_file( $file_path ) {

	$handle = fopen( $file_path, 'rb' );

	if ( false === $handle ) {
		return new WP_Error( 'wpbs_backup_open_failed', __( 'The backup file could not be opened.', 'wp-booking-system' ) );
	}

	$start = wpbs_backup_stream_skip_whitespace( $handle );

	if ( '{' !== $start ) {
		fclose( $handle );
		return new WP_Error( 'wpbs_backup_invalid_json', __( 'The backup file is not a valid WPBS backup.', 'wp-booking-system' ) );
	}

	$plugin_version_found = false;

	while ( true ) {

		$next = wpbs_backup_stream_peek_non_whitespace( $handle );

		if ( false === $next ) {
			fclose( $handle );
			return new WP_Error( 'wpbs_backup_unexpected_eof', __( 'The backup file appears to be incomplete or corrupted.', 'wp-booking-system' ) );
		}

		// End of top-level object
		if ( '}' === $next ) {
			wpbs_backup_stream_skip_whitespace( $handle );
			break;
		}

		$key = wpbs_backup_stream_read_string( $handle );

		if ( false === $key ) {
			fclose( $handle );
			return new WP_Error( 'wpbs_backup_invalid_key', __( 'The backup file contains invalid JSON keys.', 'wp-booking-system' ) );
		}

		if ( ! wpbs_backup_stream_expect_char( $handle, ':' ) ) {
			fclose( $handle );
			return new WP_Error( 'wpbs_backup_invalid_syntax', __( 'The backup file contains invalid JSON syntax.', 'wp-booking-system' ) );
		}

		if ( 'plugin_version' === $key ) {

			$raw_value = wpbs_backup_stream_read_value_raw( $handle );

			if ( false === $raw_value ) {
				fclose( $handle );
				return new WP_Error( 'wpbs_backup_invalid_version', __( 'The backup file version could not be read.', 'wp-booking-system' ) );
			}

			$decoded_value = json_decode( $raw_value, true );

			if ( JSON_ERROR_NONE !== json_last_error() || empty( $decoded_value ) ) {
				fclose( $handle );
				return new WP_Error( 'wpbs_backup_invalid_version', __( 'The file you are trying to import is not compatible with this version of the plugin.', 'wp-booking-system' ) );
			}

			$plugin_version_found = true;

		} else {

			if ( ! wpbs_backup_stream_skip_value( $handle ) ) {
				fclose( $handle );
				return new WP_Error( 'wpbs_backup_invalid_value', __( 'The backup file contains invalid JSON data.', 'wp-booking-system' ) );
			}
		}

		$separator = wpbs_backup_stream_skip_whitespace( $handle );

		if ( '}' === $separator ) {
			break;
		}

		if ( ',' !== $separator ) {
			fclose( $handle );
			return new WP_Error( 'wpbs_backup_invalid_separator', __( 'The backup file contains invalid JSON separators.', 'wp-booking-system' ) );
		}
	}

	fclose( $handle );

	if ( ! $plugin_version_found ) {
		return new WP_Error( 'wpbs_backup_missing_version', __( 'The file you are trying to import is not compatible with this version of the plugin.', 'wp-booking-system' ) );
	}

	return true;
}


/**
 * Imports the backup file progressively without loading the full JSON into memory.
 * 
 */
function wpbs_backup_stream_import_file( $file_path ) {

	global $wpdb;

	$tables = wp_booking_system()->db;

	// Empty all registered tables only after validation passed
	foreach ( $tables as $table_slug => $db_object ) {

		$table_name = str_replace( '`', '``', $db_object->table_name );
		$result     = $wpdb->query( "TRUNCATE TABLE `{$table_name}`" );

		if ( false === $result ) {
			return new WP_Error(
				'wpbs_backup_truncate_failed',
				sprintf(
					/* translators: %s: database table name */
					__( 'Could not empty table %s before import.', 'wp-booking-system' ),
					$db_object->table_name
				)
			);
		}
	}

	$handle = fopen( $file_path, 'rb' );

	if ( false === $handle ) {
		return new WP_Error( 'wpbs_backup_open_failed', __( 'The backup file could not be opened.', 'wp-booking-system' ) );
	}

	$start = wpbs_backup_stream_skip_whitespace( $handle );

	if ( '{' !== $start ) {
		fclose( $handle );
		return new WP_Error( 'wpbs_backup_invalid_json', __( 'The backup file is not a valid WPBS backup.', 'wp-booking-system' ) );
	}

	$settings = null;

	while ( true ) {

		$next = wpbs_backup_stream_peek_non_whitespace( $handle );

		if ( false === $next ) {
			fclose( $handle );
			return new WP_Error( 'wpbs_backup_unexpected_eof', __( 'The backup file appears to be incomplete or corrupted.', 'wp-booking-system' ) );
		}

		// End of top-level object
		if ( '}' === $next ) {
			wpbs_backup_stream_skip_whitespace( $handle );
			break;
		}

		$key = wpbs_backup_stream_read_string( $handle );

		if ( false === $key ) {
			fclose( $handle );
			return new WP_Error( 'wpbs_backup_invalid_key', __( 'The backup file contains invalid JSON keys.', 'wp-booking-system' ) );
		}

		if ( ! wpbs_backup_stream_expect_char( $handle, ':' ) ) {
			fclose( $handle );
			return new WP_Error( 'wpbs_backup_invalid_syntax', __( 'The backup file contains invalid JSON syntax.', 'wp-booking-system' ) );
		}

		if ( isset( $tables[ $key ] ) ) {

			$import_result = wpbs_backup_stream_import_table_rows( $handle, $tables[ $key ]->table_name );

			if ( is_wp_error( $import_result ) ) {
				fclose( $handle );
				return $import_result;
			}

		} elseif ( 'settings' === $key ) {

			$raw_value = wpbs_backup_stream_read_value_raw( $handle );

			if ( false === $raw_value ) {
				fclose( $handle );
				return new WP_Error( 'wpbs_backup_invalid_settings', __( 'The plugin settings could not be read from the backup file.', 'wp-booking-system' ) );
			}

			$decoded_settings = json_decode( $raw_value, true );

			if ( JSON_ERROR_NONE !== json_last_error() ) {
				fclose( $handle );
				return new WP_Error( 'wpbs_backup_invalid_settings', __( 'The plugin settings in the backup file are invalid.', 'wp-booking-system' ) );
			}

			$settings = $decoded_settings;

		} else {

			if ( ! wpbs_backup_stream_skip_value( $handle ) ) {
				fclose( $handle );
				return new WP_Error( 'wpbs_backup_invalid_value', __( 'The backup file contains invalid JSON data.', 'wp-booking-system' ) );
			}
		}

		$separator = wpbs_backup_stream_skip_whitespace( $handle );

		if ( '}' === $separator ) {
			break;
		}

		if ( ',' !== $separator ) {
			fclose( $handle );
			return new WP_Error( 'wpbs_backup_invalid_separator', __( 'The backup file contains invalid JSON separators.', 'wp-booking-system' ) );
		}
	}

	fclose( $handle );

	// Restore settings after table import
	if ( null !== $settings ) {
		update_option( 'wpbs_settings', $settings );
	}

	return true;
}


/**
 * Imports one table array progressively.
 *
 */
function wpbs_backup_stream_import_table_rows( $handle, $table_name ) {

	global $wpdb;

	$start = wpbs_backup_stream_skip_whitespace( $handle );

	if ( '[' !== $start ) {
		return new WP_Error(
			'wpbs_backup_invalid_table_array',
			sprintf(
				/* translators: %s: database table name */
				__( 'The backup data for table %s is invalid.', 'wp-booking-system' ),
				$table_name
			)
		);
	}

	$row_count = 0;

	while ( true ) {

		$next = wpbs_backup_stream_peek_non_whitespace( $handle );

		if ( false === $next ) {
			return new WP_Error(
				'wpbs_backup_unexpected_eof',
				sprintf(
					/* translators: %s: database table name */
					__( 'The backup data for table %s appears to be incomplete.', 'wp-booking-system' ),
					$table_name
				)
			);
		}

		// Empty array
		if ( ']' === $next ) {
			wpbs_backup_stream_skip_whitespace( $handle );
			break;
		}

		$row_raw = wpbs_backup_stream_read_value_raw( $handle );

		if ( false === $row_raw ) {
			return new WP_Error(
				'wpbs_backup_invalid_row',
				sprintf(
					/* translators: %s: database table name */
					__( 'A row from table %s could not be read.', 'wp-booking-system' ),
					$table_name
				)
			);
		}

		$row = json_decode( $row_raw, true );

		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $row ) ) {
			return new WP_Error(
				'wpbs_backup_invalid_row',
				sprintf(
					/* translators: %s: database table name */
					__( 'A row from table %s is invalid.', 'wp-booking-system' ),
					$table_name
				)
			);
		}

		$insert_result = $wpdb->insert( $table_name, $row );

		if ( false === $insert_result ) {
			return new WP_Error(
				'wpbs_backup_insert_failed',
				sprintf(
					/* translators: 1: database table name, 2: database error */
					__( 'Import failed while inserting data into table %1$s. Database error: %2$s', 'wp-booking-system' ),
					$table_name,
					$wpdb->last_error
				)
			);
		}

		$row_count++;

		if ( 0 === $row_count % 100 ) {
			if ( function_exists( 'gc_collect_cycles' ) ) {
				gc_collect_cycles();
			}
		}

		$separator = wpbs_backup_stream_skip_whitespace( $handle );

		if ( ']' === $separator ) {
			break;
		}

		if ( ',' !== $separator ) {
			return new WP_Error(
				'wpbs_backup_invalid_row_separator',
				sprintf(
					/* translators: %s: database table name */
					__( 'The row separator in table %s is invalid.', 'wp-booking-system' ),
					$table_name
				)
			);
		}
	}

	return true;
}


/**
 * Reads the next non-whitespace character and consumes it.
 * 
 */
function wpbs_backup_stream_skip_whitespace( $handle ) {

	while ( false !== ( $char = fgetc( $handle ) ) ) {
		if ( ! ctype_space( $char ) ) {
			return $char;
		}
	}

	return false;
}


/**
 * Peeks the next non-whitespace character without consuming it.
 * 
 */
function wpbs_backup_stream_peek_non_whitespace( $handle ) {

	$position = ftell( $handle );

	if ( false === $position ) {
		return false;
	}

	$char = wpbs_backup_stream_skip_whitespace( $handle );

	fseek( $handle, $position );

	return $char;
}


/**
 * Expects a specific character after optional whitespace.
 * 
 */
function wpbs_backup_stream_expect_char( $handle, $expected ) {
	return ( $expected === wpbs_backup_stream_skip_whitespace( $handle ) );
}


/**
 * Reads a JSON string and returns the decoded PHP string.
 * 
 */
function wpbs_backup_stream_read_string( $handle ) {

	$start = wpbs_backup_stream_skip_whitespace( $handle );

	if ( '"' !== $start ) {
		return false;
	}

	$raw     = '"';
	$escaped = false;
	$closed  = false;

	while ( false !== ( $char = fgetc( $handle ) ) ) {

		$raw .= $char;

		if ( $escaped ) {
			$escaped = false;
			continue;
		}

		if ( '\\' === $char ) {
			$escaped = true;
			continue;
		}

		if ( '"' === $char ) {
			$closed = true;
			break;
		}
	}

	if ( ! $closed ) {
		return false;
	}

	$decoded = json_decode( $raw, true );

	if ( JSON_ERROR_NONE !== json_last_error() ) {
		return false;
	}

	return $decoded;
}


/**
 * Reads the next JSON value as raw JSON text.
 * 
 */
function wpbs_backup_stream_read_value_raw( $handle ) {

	$start = wpbs_backup_stream_skip_whitespace( $handle );

	if ( false === $start ) {
		return false;
	}

	// Object or array
	if ( '{' === $start || '[' === $start ) {

		$raw       = $start;
		$stack     = array( $start );
		$in_string = false;
		$escaped   = false;

		while ( ! empty( $stack ) && false !== ( $char = fgetc( $handle ) ) ) {

			$raw .= $char;

			if ( $in_string ) {

				if ( $escaped ) {
					$escaped = false;
					continue;
				}

				if ( '\\' === $char ) {
					$escaped = true;
					continue;
				}

				if ( '"' === $char ) {
					$in_string = false;
				}

				continue;
			}

			if ( '"' === $char ) {
				$in_string = true;
				continue;
			}

			if ( '{' === $char || '[' === $char ) {
				$stack[] = $char;
				continue;
			}

			if ( '}' === $char || ']' === $char ) {

				$last = array_pop( $stack );

				if (
					( '{' === $last && '}' !== $char ) ||
					( '[' === $last && ']' !== $char )
				) {
					return false;
				}
			}
		}

		return empty( $stack ) ? $raw : false;
	}

	// String
	if ( '"' === $start ) {

		$raw     = '"';
		$escaped = false;
		$closed  = false;

		while ( false !== ( $char = fgetc( $handle ) ) ) {

			$raw .= $char;

			if ( $escaped ) {
				$escaped = false;
				continue;
			}

			if ( '\\' === $char ) {
				$escaped = true;
				continue;
			}

			if ( '"' === $char ) {
				$closed = true;
				break;
			}
		}

		return $closed ? $raw : false;
	}

	// Primitive: true, false, null, number
	$raw = $start;

	while ( false !== ( $char = fgetc( $handle ) ) ) {

		if ( ctype_space( $char ) || ',' === $char || ']' === $char || '}' === $char ) {
			fseek( $handle, -1, SEEK_CUR );
			break;
		}

		$raw .= $char;
	}

	return $raw;
}


/**
 * Skips the next JSON value without storing it.
 * Used during validation to avoid building large strings in memory.
 * 
 */
function wpbs_backup_stream_skip_value( $handle ) {

	$start = wpbs_backup_stream_skip_whitespace( $handle );

	if ( false === $start ) {
		return false;
	}

	// Object or array
	if ( '{' === $start || '[' === $start ) {

		$stack     = array( $start );
		$in_string = false;
		$escaped   = false;

		while ( ! empty( $stack ) && false !== ( $char = fgetc( $handle ) ) ) {

			if ( $in_string ) {

				if ( $escaped ) {
					$escaped = false;
					continue;
				}

				if ( '\\' === $char ) {
					$escaped = true;
					continue;
				}

				if ( '"' === $char ) {
					$in_string = false;
				}

				continue;
			}

			if ( '"' === $char ) {
				$in_string = true;
				continue;
			}

			if ( '{' === $char || '[' === $char ) {
				$stack[] = $char;
				continue;
			}

			if ( '}' === $char || ']' === $char ) {

				$last = array_pop( $stack );

				if (
					( '{' === $last && '}' !== $char ) ||
					( '[' === $last && ']' !== $char )
				) {
					return false;
				}
			}
		}

		return empty( $stack );
	}

	// String
	if ( '"' === $start ) {

		$escaped = false;

		while ( false !== ( $char = fgetc( $handle ) ) ) {

			if ( $escaped ) {
				$escaped = false;
				continue;
			}

			if ( '\\' === $char ) {
				$escaped = true;
				continue;
			}

			if ( '"' === $char ) {
				return true;
			}
		}

		return false;
	}

	// Primitive
	while ( false !== ( $char = fgetc( $handle ) ) ) {

		if ( ctype_space( $char ) || ',' === $char || ']' === $char || '}' === $char ) {
			fseek( $handle, -1, SEEK_CUR );
			break;
		}
	}

	return true;
}