<?php
/**
 * Property Translation Report Page
 *
 * Provides a dashboard interface to track English <-> Spanish translations for properties.
 *
 * @package PropertyTranslationReport
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if WPML is active and configured correctly.
 */
function ptr_is_wpml_active() {
	global $wpdb;
	if ( ! class_exists( 'SitePress' ) && ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
		return false;
	}
	// Check if the translations table exists.
	$table_name = $wpdb->prefix . 'icl_translations';
	if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
		return false;
	}
	return true;
}

/**
 * Register Submenu under Properties.
 */
add_action( 'admin_menu', 'ptr_register_submenu' );
function ptr_register_submenu() {
	if ( ! ptr_is_wpml_active() ) {
		// Register a dummy notice or submenu to warn if WPML is not active.
		add_submenu_page(
			'edit.php?post_type=property',
			__( 'Property Translation Report', 'property-translation-report' ),
			__( 'Translation Report', 'property-translation-report' ),
			'manage_options',
			'property-translation-report',
			'ptr_render_inactive_notice'
		);
		return;
	}

	add_submenu_page(
		'edit.php?post_type=property',
		__( 'Property Translation Report', 'property-translation-report' ),
		__( 'Translation Report', 'property-translation-report' ),
		'edit_posts',
		'property-translation-report',
		'ptr_render_report_page'
	);
}

/**
 * Render page when WPML is inactive.
 */
function ptr_render_inactive_notice() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Property Translation Report', 'property-translation-report' ); ?></h1>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'WPML is either inactive, not installed, or its database table (wp_icl_translations) is missing. Please make sure WPML is active and configured correctly for Custom Post Type properties.', 'property-translation-report' ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * Fetch overall summary counts using optimized SQL.
 */
function ptr_get_summary_counts() {
	global $wpdb;
	
	$table_translations = $wpdb->prefix . 'icl_translations';
	$table_posts = $wpdb->posts;

	$query = "
		SELECT 
			-- Global tab counts
			COUNT(CASE WHEN en_status = 'publish' OR (en_id IS NULL AND es_status = 'publish') THEN 1 END) as count_published,
			COUNT(CASE WHEN en_status = 'draft' OR (en_id IS NULL AND es_status = 'draft') THEN 1 END) as count_draft,
			COUNT(CASE WHEN en_status = 'trash' OR (en_id IS NULL AND es_status = 'trash') THEN 1 END) as count_trash,
			COUNT(CASE WHEN en_status IN ('publish', 'draft', 'trash') OR (en_id IS NULL AND es_status IN ('publish', 'draft', 'trash')) THEN 1 END) as count_all,

			-- Published sub-counts
			COUNT(CASE WHEN en_id IS NOT NULL AND en_status = 'publish' THEN 1 END) as pub_has_en,
			COUNT(CASE WHEN es_id IS NOT NULL AND es_status = 'publish' THEN 1 END) as pub_has_es,
			COUNT(CASE WHEN (en_id IS NULL OR en_status != 'publish') AND es_status = 'publish' THEN 1 END) as pub_missing_en,
			COUNT(CASE WHEN (es_id IS NULL OR es_status != 'publish') AND en_status = 'publish' THEN 1 END) as pub_missing_es,

			-- Draft sub-counts
			COUNT(CASE WHEN en_id IS NOT NULL AND en_status = 'draft' THEN 1 END) as draft_has_en,
			COUNT(CASE WHEN es_id IS NOT NULL AND es_status = 'draft' THEN 1 END) as draft_has_es,
			COUNT(CASE WHEN (en_id IS NULL OR en_status != 'draft') AND es_status = 'draft' THEN 1 END) as draft_missing_en,
			COUNT(CASE WHEN (es_id IS NULL OR es_status != 'draft') AND en_status = 'draft' THEN 1 END) as draft_missing_es,

			-- Trash sub-counts
			COUNT(CASE WHEN en_id IS NOT NULL AND en_status = 'trash' THEN 1 END) as trash_has_en,
			COUNT(CASE WHEN es_id IS NOT NULL AND es_status = 'trash' THEN 1 END) as trash_has_es,
			COUNT(CASE WHEN (en_id IS NULL OR en_status != 'trash') AND es_status = 'trash' THEN 1 END) as trash_missing_en,
			COUNT(CASE WHEN (es_id IS NULL OR es_status != 'trash') AND en_status = 'trash' THEN 1 END) as trash_missing_es,

			-- All sub-counts
			COUNT(CASE WHEN en_id IS NOT NULL AND en_status IN ('publish', 'draft', 'trash') THEN 1 END) as all_has_en,
			COUNT(CASE WHEN es_id IS NOT NULL AND es_status IN ('publish', 'draft', 'trash') THEN 1 END) as all_has_es,
			COUNT(CASE WHEN en_id IS NULL AND es_status IN ('publish', 'draft', 'trash') THEN 1 END) as all_missing_en,
			COUNT(CASE WHEN es_id IS NULL AND en_status IN ('publish', 'draft', 'trash') THEN 1 END) as all_missing_es
		FROM (
			SELECT 
				tr.trid,
				MAX(CASE WHEN tr.language_code = 'en' THEN tr.element_id END) as en_id,
				MAX(CASE WHEN tr.language_code = 'es' THEN tr.element_id END) as es_id,
				MAX(CASE WHEN tr.language_code = 'en' THEN p_en.post_status END) as en_status,
				MAX(CASE WHEN tr.language_code = 'es' THEN p_es.post_status END) as es_status
			FROM {$table_translations} tr
			LEFT JOIN {$table_posts} p_en ON p_en.ID = tr.element_id AND tr.language_code = 'en' AND p_en.post_type = 'property'
			LEFT JOIN {$table_posts} p_es ON p_es.ID = tr.element_id AND tr.language_code = 'es' AND p_es.post_type = 'property'
			WHERE tr.element_type = 'post_property'
			GROUP BY tr.trid
		) t
		WHERE (en_id IS NOT NULL OR es_id IS NOT NULL)
	";

	$results = $wpdb->get_row( $query, ARRAY_A );

	return array(
		'all'             => isset( $results['count_all'] ) ? (int) $results['count_all'] : 0,
		'publish'         => isset( $results['count_published'] ) ? (int) $results['count_published'] : 0,
		'draft'           => isset( $results['count_draft'] ) ? (int) $results['count_draft'] : 0,
		'trash'           => isset( $results['count_trash'] ) ? (int) $results['count_trash'] : 0,

		// Sub-counts
		'all_has_en'      => isset( $results['all_has_en'] ) ? (int) $results['all_has_en'] : 0,
		'all_has_es'      => isset( $results['all_has_es'] ) ? (int) $results['all_has_es'] : 0,
		'all_missing_en'  => isset( $results['all_missing_en'] ) ? (int) $results['all_missing_en'] : 0,
		'all_missing_es'  => isset( $results['all_missing_es'] ) ? (int) $results['all_missing_es'] : 0,

		'pub_has_en'      => isset( $results['pub_has_en'] ) ? (int) $results['pub_has_en'] : 0,
		'pub_has_es'      => isset( $results['pub_has_es'] ) ? (int) $results['pub_has_es'] : 0,
		'pub_missing_en'  => isset( $results['pub_missing_en'] ) ? (int) $results['pub_missing_en'] : 0,
		'pub_missing_es'  => isset( $results['pub_missing_es'] ) ? (int) $results['pub_missing_es'] : 0,

		'draft_has_en'    => isset( $results['draft_has_en'] ) ? (int) $results['draft_has_en'] : 0,
		'draft_has_es'    => isset( $results['draft_has_es'] ) ? (int) $results['draft_has_es'] : 0,
		'draft_missing_en'=> isset( $results['draft_missing_en'] ) ? (int) $results['draft_missing_en'] : 0,
		'draft_missing_es'=> isset( $results['draft_missing_es'] ) ? (int) $results['draft_missing_es'] : 0,

		'trash_has_en'    => isset( $results['trash_has_en'] ) ? (int) $results['trash_has_en'] : 0,
		'trash_has_es'    => isset( $results['trash_has_es'] ) ? (int) $results['trash_has_es'] : 0,
		'trash_missing_en'=> isset( $results['trash_missing_en'] ) ? (int) $results['trash_missing_en'] : 0,
		'trash_missing_es'=> isset( $results['trash_missing_es'] ) ? (int) $results['trash_missing_es'] : 0,
	);
}

/**
 * Helper to build filter query clauses.
 */
function ptr_build_filter_clauses( $args ) {
	global $wpdb;
	
	$where = array();
	$join = "";

	// Tab filtering (uses OR check to allow cross-matching properties to appear in both tabs)
	if ( empty( $args['tab'] ) || $args['tab'] === 'all' ) {
		$where[] = "(en_post.post_status IN ('publish', 'draft', 'trash') OR es_post.post_status IN ('publish', 'draft', 'trash'))";
	} else {
		$tab = sanitize_key( $args['tab'] );
		$where[] = $wpdb->prepare( "(en_post.post_status = %s OR es_post.post_status = %s)", $tab, $tab );
	}

	// Subtab translation status sub-filtering (supporting cross-matching validation)
	if ( ! empty( $args['subtab'] ) && $args['subtab'] !== 'all' ) {
		$subtab = sanitize_key( $args['subtab'] );
		$tab = ! empty( $args['tab'] ) ? sanitize_key( $args['tab'] ) : 'all';

		if ( $subtab === 'has_en' ) {
			if ( $tab === 'all' ) {
				$where[] = "en_post.ID IS NOT NULL AND en_post.post_status IN ('publish', 'draft', 'trash')";
			} else {
				$where[] = $wpdb->prepare( "en_post.ID IS NOT NULL AND en_post.post_status = %s", $tab );
			}
		} elseif ( $subtab === 'has_es' ) {
			if ( $tab === 'all' ) {
				$where[] = "es_post.ID IS NOT NULL AND es_post.post_status IN ('publish', 'draft', 'trash')";
			} else {
				$where[] = $wpdb->prepare( "es_post.ID IS NOT NULL AND es_post.post_status = %s", $tab );
			}
		} elseif ( $subtab === 'missing_en' ) {
			if ( $tab === 'all' ) {
				$where[] = "en_post.ID IS NULL";
			} else {
				$where[] = $wpdb->prepare( "(en_post.ID IS NULL OR en_post.post_status != %s) AND es_post.post_status = %s", $tab, $tab );
			}
		} elseif ( $subtab === 'missing_es' ) {
			if ( $tab === 'all' ) {
				$where[] = "es_post.ID IS NULL";
			} else {
				$where[] = $wpdb->prepare( "(es_post.ID IS NULL OR es_post.post_status != %s) AND en_post.post_status = %s", $tab, $tab );
			}
		}
	}

	// Search query
	if ( ! empty( $args['s'] ) ) {
		$search_term = '%' . $wpdb->esc_like( sanitize_text_field( $args['s'] ) ) . '%';
		$where[] = $wpdb->prepare(
			"(en_post.post_title LIKE %s OR es_post.post_title LIKE %s OR en_meta.meta_value LIKE %s OR es_meta.meta_value LIKE %s)",
			$search_term,
			$search_term,
			$search_term,
			$search_term
		);
	}

	$where_sql = ! empty( $where ) ? ' AND ' . implode( ' AND ', $where ) : '';

	// Sorting
	$orderby = 'MAX(en_post.post_title)';
	if ( ! empty( $args['orderby'] ) ) {
		$allowed_orderby = array(
			'english_property_id' => 'MAX(en_meta.meta_value)',
			'english_title'       => 'MAX(en_post.post_title)',
			'english_status'      => 'MAX(en_post.post_status)',
			'spanish_property_id' => 'MAX(es_meta.meta_value)',
			'spanish_title'       => 'MAX(es_post.post_title)',
			'spanish_status'      => 'MAX(es_post.post_status)',
		);
		if ( array_key_exists( $args['orderby'], $allowed_orderby ) ) {
			$orderby = $allowed_orderby[ $args['orderby'] ];
		}
	}

	$order = 'ASC';
	if ( ! empty( $args['order'] ) && in_array( strtoupper( $args['order'] ), array( 'ASC', 'DESC' ), true ) ) {
		$order = strtoupper( $args['order'] );
	}

	$order_sql = " ORDER BY {$orderby} {$order}";

	return array(
		'where' => $where_sql,
		'order' => $order_sql,
	);
}

/**
 * Fetch paginated master post IDs matching the filters and search.
 */
function ptr_get_master_ids( $args ) {
	global $wpdb;

	$table_translations = $wpdb->prefix . 'icl_translations';
	$table_posts = $wpdb->posts;
	$table_postmeta = $wpdb->postmeta;

	$clauses = ptr_build_filter_clauses( $args );

	$limit = isset( $args['per_page'] ) ? absint( $args['per_page'] ) : 20;
	$page = isset( $args['paged'] ) ? absint( $args['paged'] ) : 1;
	$offset = ( $page - 1 ) * $limit;

	$query = $wpdb->prepare(
		"
		SELECT 
			COALESCE(MAX(en_post.ID), MAX(es_post.ID)) as master_post_id
		FROM (
			SELECT trid,
				   MAX(CASE WHEN language_code = 'en' THEN element_id END) as en_id,
				   MAX(CASE WHEN language_code = 'es' THEN element_id END) as es_id
			FROM {$table_translations}
			WHERE element_type = 'post_property'
			GROUP BY trid
		) t
		LEFT JOIN {$table_posts} en_post ON en_post.ID = t.en_id AND en_post.post_type = 'property'
		LEFT JOIN {$table_postmeta} en_meta ON en_meta.post_id = t.en_id AND en_meta.meta_key = 'property_id'
		LEFT JOIN {$table_posts} es_post ON es_post.ID = t.es_id AND es_post.post_type = 'property'
		LEFT JOIN {$table_postmeta} es_meta ON es_meta.post_id = t.es_id AND es_meta.meta_key = 'property_id'
		WHERE (en_post.ID IS NOT NULL OR es_post.ID IS NOT NULL)
		{$clauses['where']}
		GROUP BY t.trid
		{$clauses['order']}
		LIMIT %d OFFSET %d
		",
		$limit,
		$offset
	);

	return $wpdb->get_col( $query );
}

/**
 * Fetch total counts for pagination matching filters and search.
 */
function ptr_get_master_ids_count( $args ) {
	global $wpdb;

	$table_translations = $wpdb->prefix . 'icl_translations';
	$table_posts = $wpdb->posts;
	$table_postmeta = $wpdb->postmeta;

	$clauses = ptr_build_filter_clauses( $args );

	$query = "
		SELECT 
			COUNT(DISTINCT t.trid)
		FROM (
			SELECT trid,
				   MAX(CASE WHEN language_code = 'en' THEN element_id END) as en_id,
				   MAX(CASE WHEN language_code = 'es' THEN element_id END) as es_id
			FROM {$table_translations}
			WHERE element_type = 'post_property'
			GROUP BY trid
		) t
		LEFT JOIN {$table_posts} en_post ON en_post.ID = t.en_id AND en_post.post_type = 'property'
		LEFT JOIN {$table_postmeta} en_meta ON en_meta.post_id = t.en_id AND en_meta.meta_key = 'property_id'
		LEFT JOIN {$table_posts} es_post ON es_post.ID = t.es_id AND es_post.post_type = 'property'
		LEFT JOIN {$table_postmeta} es_meta ON es_meta.post_id = t.es_id AND es_meta.meta_key = 'property_id'
		WHERE (en_post.ID IS NOT NULL OR es_post.ID IS NOT NULL)
		{$clauses['where']}
	";

	return (int) $wpdb->get_var( $query );
}

/**
 * Render the Translation Report Page.
 */
function ptr_render_report_page() {
	// Parse parameters
	$tab     = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'all';
	$subtab  = isset( $_GET['subtab'] ) ? sanitize_key( $_GET['subtab'] ) : 'all';
	$s       = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
	$orderby = isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : 'english_title';
	$order   = isset( $_GET['order'] ) ? sanitize_key( $_GET['order'] ) : 'asc';
	$paged   = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	
	$per_page = 20;

	// Fetch Summary Counts
	$summary_counts = ptr_get_summary_counts();

	// Query paginated master IDs
	$query_args = array(
		'tab'      => $tab,
		'subtab'   => $subtab,
		's'        => $s,
		'orderby'  => $orderby,
		'order'    => $order,
		'paged'    => $paged,
		'per_page' => $per_page,
	);
	$master_ids = ptr_get_master_ids( $query_args );
	$total_items = ptr_get_master_ids_count( $query_args );
	$total_pages = ceil( $total_items / $per_page );

	// Fetch Post Objects using WP_Query
	$mapped_records = array();
	if ( ! empty( $master_ids ) ) {
		$master_query = new WP_Query( array(
			'post_type'        => 'property',
			'post__in'         => $master_ids,
			'posts_per_page'   => -1,
			'post_status'      => array( 'publish', 'draft', 'trash', 'pending', 'future', 'private', 'inherit' ),
			'orderby'          => 'post__in',
			'suppress_filters' => true,
		) );

		if ( $master_query->have_posts() ) {
			while ( $master_query->have_posts() ) {
				$master_query->the_post();
				$master_post_id = get_the_ID();

				// Get translation group ID for current master ID
				$trid = apply_filters( 'wpml_element_trid', null, $master_post_id, 'post_property' );
				if ( ! $trid ) {
					continue;
				}

				// Fetch translation group from WPML API
				$translations = apply_filters( 'wpml_get_element_translations', null, $trid, 'post_property' );
				
				// Resolve English translation status
				$en_id = null;
				$en_status_label = '';
				$en_title = '';
				$en_property_id = '';
				$en_error_type = ''; // 'missing', 'orphan'

				if ( isset( $translations['en'] ) ) {
					$en_id = (int) $translations['en']->element_id;
					$post_status = get_post_status( $en_id );
					if ( $post_status ) {
						$en_title = get_the_title( $en_id );
						$en_status_label = ucfirst( $post_status );
						$en_property_id = get_post_meta( $en_id, 'property_id', true );
					} else {
						$en_error_type = 'orphan';
					}
				} else {
					$en_error_type = 'missing';
				}

				// Resolve Spanish translation status
				$es_id = null;
				$es_status_label = '';
				$es_title = '';
				$es_property_id = '';
				$es_error_type = ''; // 'missing', 'orphan'

				if ( isset( $translations['es'] ) ) {
					$es_id = (int) $translations['es']->element_id;
					$post_status = get_post_status( $es_id );
					if ( $post_status ) {
						$es_title = get_the_title( $es_id );
						$es_status_label = ucfirst( $post_status );
						$es_property_id = get_post_meta( $es_id, 'property_id', true );
					} else {
						$es_error_type = 'orphan';
					}
				} else {
					$es_error_type = 'missing';
				}

				$mapped_records[] = array(
					'en_id'          => $en_id,
					'en_title'       => $en_title,
					'en_status'      => $en_status_label,
					'en_property_id' => $en_property_id,
					'en_error'       => $en_error_type,
					
					'es_id'          => $es_id,
					'es_title'       => $es_title,
					'es_status'      => $es_status_label,
					'es_property_id' => $es_property_id,
					'es_error'       => $es_error_type,
				);
			}
			wp_reset_postdata();
		}
	}

	// Generate Nonce for Export
	$export_nonce = wp_create_nonce( 'property_report_export_nonce' );
	$export_url = add_query_arg( array(
		'action'   => 'export_property_report',
		'tab'      => $tab,
		'subtab'   => $subtab,
		's'        => $s,
		'orderby'  => $orderby,
		'order'    => $order,
		'_wpnonce' => $export_nonce,
	), admin_url( 'admin-post.php' ) );

	// Build base pagination links
	$current_url = admin_url( 'edit.php?post_type=property&page=property-translation-report' );

	?>
	<div class="wrap ptr-dashboard">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Property Translation Report', 'property-translation-report' ); ?></h1>
		
		<!-- Excel Export & Debug Toggle Controls -->
		<div class="ptr-header-actions">
			<button id="ptr-toggle-debug-cols" class="button button-secondary">
				<span class="dashicons dashicons-visibility ptr-dashicon-left"></span>
				<span class="btn-text"><?php esc_html_e( 'Show WP IDs', 'property-translation-report' ); ?></span>
			</button>
			<a href="<?php echo esc_url( $export_url ); ?>" class="button button-primary">
				<span class="dashicons dashicons-media-spreadsheet ptr-dashicon-left"></span>
				<?php esc_html_e( 'Export Excel / CSV', 'property-translation-report' ); ?>
			</a>
		</div>
		<hr class="wp-header-end">

		<!-- Custom Modern Dashboard CSS -->
		<style>
			.ptr-subsubsub {
				list-style: none;
				margin: 8px 0 5px;
				padding: 0;
				font-size: 13px;
				float: left;
				color: #64748b;
			}
			.ptr-subsubsub li {
				display: inline-block;
				margin: 0;
				padding: 0;
			}
			.ptr-subsubsub a {
				line-height: 2;
				padding: .2em;
				text-decoration: none;
			}
			.ptr-subsubsub a.current {
				font-weight: 600;
				color: #000;
			}
			/* Hide WPML default views list */
			.ptr-dashboard ul.subsubsub {
				display: none !important;
			}

			.ptr-dashboard {
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
			}
			.ptr-header-actions {
				float: right;
				margin-top: 10px;
				display: flex;
				gap: 10px;
			}
			.ptr-dashicon-left {
				margin-right: 5px;
				vertical-align: middle;
				line-height: 1.3;
			}
			/* Summary Metrics Grid styling */
			.ptr-summary-grid {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
				gap: 16px;
				margin: 20px 0 25px 0;
			}
			.ptr-card {
				background: rgba(255, 255, 255, 0.85);
				border: 1px solid #e2e8f0;
				border-radius: 8px;
				padding: 16px 20px;
				box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
				transition: transform 0.2s ease, box-shadow 0.2s ease;
				position: relative;
				overflow: hidden;
				backdrop-filter: blur(10px);
			}
			.ptr-card:hover {
				transform: translateY(-2px);
				box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
			}
			.ptr-card::before {
				content: '';
				position: absolute;
				top: 0;
				left: 0;
				width: 4px;
				height: 100%;
				background: #cbd5e1;
			}
			.ptr-card.card-all::before { background: #3b82f6; }
			.ptr-card.card-publish::before { background: #10b981; }
			.ptr-card.card-draft::before { background: #f59e0b; }
			.ptr-card.card-trash::before { background: #64748b; }
			.ptr-card.card-missing-en::before { background: #ef4444; }
			.ptr-card.card-missing-es::before { background: #ef4444; }

			.ptr-card-title {
				font-size: 13px;
				color: #64748b;
				font-weight: 500;
				margin: 0 0 8px 0;
				text-transform: uppercase;
				letter-spacing: 0.5px;
			}
			.ptr-card-value {
				font-size: 26px;
				font-weight: 700;
				color: #1e293b;
				line-height: 1;
			}
			
			/* Badges & Error Styling */
			.ptr-badge {
				display: inline-block;
				font-size: 11px;
				font-weight: 600;
				padding: 3px 8px;
				border-radius: 4px;
				text-align: center;
			}
			.ptr-badge-missing {
				background: #fdf2f2;
				color: #de350b;
				border: 1px solid #fbd5d5;
			}
			.ptr-badge-orphan {
				background: #fff5f5;
				color: #e53e3e;
				border: 1px dashed #feb2b2;
				font-style: italic;
			}
			.ptr-text-muted {
				color: #94a3b8;
			}

			/* Debug Column Visibility */
			.ptr-debug-col {
				display: none;
			}
			.show-debug-cols .ptr-debug-col {
				display: table-cell;
			}

			/* Modern Table Adjustments */
			.wp-list-table {
				box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
				border-radius: 6px;
				overflow: hidden;
			}
			.wp-list-table th {
				font-weight: 600;
				color: #334155;
				background-color: #f8fafc;
				border-bottom: 2px solid #e2e8f0 !important;
			}
			.wp-list-table tbody tr:hover {
				background-color: #f8fafc;
			}
			
			.ptr-search-box {
				margin-bottom: 15px;
				float: right;
			}
			
			.tablenav .actions {
				overflow: visible;
			}
		</style>

		<!-- Javascript for Show/Hide Post ID Debug Column -->
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				var toggleBtn = document.getElementById('ptr-toggle-debug-cols');
				var dashboard = document.querySelector('.ptr-dashboard');
				
				// Read state from localStorage to preserve across page refreshes
				var showDebug = localStorage.getItem('ptr_show_debug') === 'true';
				if (showDebug) {
					dashboard.classList.add('show-debug-cols');
					updateButtonState(true);
				}

				toggleBtn.addEventListener('click', function(e) {
					e.preventDefault();
					var isActive = dashboard.classList.toggle('show-debug-cols');
					localStorage.setItem('ptr_show_debug', isActive);
					updateButtonState(isActive);
				});

				function updateButtonState(active) {
					var textEl = toggleBtn.querySelector('.btn-text');
					var iconEl = toggleBtn.querySelector('.dashicons');
					if (active) {
						textEl.textContent = '<?php esc_html_e( 'Hide WP IDs', 'property-translation-report' ); ?>';
						iconEl.classList.remove('dashicons-visibility');
						iconEl.classList.add('dashicons-hidden');
					} else {
						textEl.textContent = '<?php esc_html_e( 'Show WP IDs', 'property-translation-report' ); ?>';
						iconEl.classList.remove('dashicons-hidden');
						iconEl.classList.add('dashicons-visibility');
					}
				}
			});
		</script>

		<!-- Metrics Grid Summary Cards -->
		<div class="ptr-summary-grid">
			<div class="ptr-card card-all">
				<p class="ptr-card-title"><?php esc_html_e( 'All Groups', 'property-translation-report' ); ?></p>
				<p class="ptr-card-value"><?php echo esc_html( number_format_i18n( $summary_counts['all'] ) ); ?></p>
			</div>
			<div class="ptr-card card-publish">
				<p class="ptr-card-title"><?php esc_html_e( 'Published', 'property-translation-report' ); ?></p>
				<p class="ptr-card-value"><?php echo esc_html( number_format_i18n( $summary_counts['publish'] ) ); ?></p>
			</div>
			<div class="ptr-card card-draft">
				<p class="ptr-card-title"><?php esc_html_e( 'Drafts', 'property-translation-report' ); ?></p>
				<p class="ptr-card-value"><?php echo esc_html( number_format_i18n( $summary_counts['draft'] ) ); ?></p>
			</div>
			<div class="ptr-card card-trash">
				<p class="ptr-card-title"><?php esc_html_e( 'Trash', 'property-translation-report' ); ?></p>
				<p class="ptr-card-value"><?php echo esc_html( number_format_i18n( $summary_counts['trash'] ) ); ?></p>
			</div>
			<div class="ptr-card card-missing-en">
				<p class="ptr-card-title"><?php esc_html_e( 'Missing English', 'property-translation-report' ); ?></p>
				<p class="ptr-card-value" style="color: #ef4444;"><?php echo esc_html( number_format_i18n( $summary_counts['all_missing_en'] ) ); ?></p>
			</div>
			<div class="ptr-card card-missing-es">
				<p class="ptr-card-title"><?php esc_html_e( 'Missing Spanish', 'property-translation-report' ); ?></p>
				<p class="ptr-card-value" style="color: #ef4444;"><?php echo esc_html( number_format_i18n( $summary_counts['all_missing_es'] ) ); ?></p>
			</div>
		</div>

		<!-- WP Filter / Status Views Tabs -->
		<!-- WP Filter / Status Views Tabs -->
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'all', 'paged' => 1 ), $current_url ) ); ?>" class="nav-tab <?php echo $tab === 'all' ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'All', 'property-translation-report' ); ?> 
				<span class="count">(<?php echo esc_html( $summary_counts['all'] ); ?>)</span>
			</a>
			<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'publish', 'paged' => 1 ), $current_url ) ); ?>" class="nav-tab <?php echo $tab === 'publish' ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Published', 'property-translation-report' ); ?>
				<span class="count">(<?php echo esc_html( $summary_counts['publish'] ); ?>)</span>
			</a>
			<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'draft', 'paged' => 1 ), $current_url ) ); ?>" class="nav-tab <?php echo $tab === 'draft' ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Draft', 'property-translation-report' ); ?>
				<span class="count">(<?php echo esc_html( $summary_counts['draft'] ); ?>)</span>
			</a>
			<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'trash', 'paged' => 1 ), $current_url ) ); ?>" class="nav-tab <?php echo $tab === 'trash' ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Trash', 'property-translation-report' ); ?>
				<span class="count">(<?php echo esc_html( $summary_counts['trash'] ); ?>)</span>
			</a>
		</h2>

		<!-- Sub-tab Filter Navigation -->
		<?php
		// Resolve sub-counts for active tab
		$sub_all = 0;
		$sub_has_en = 0;
		$sub_has_es = 0;
		$sub_missing_en = 0;
		$sub_missing_es = 0;

		if ( $tab === 'publish' ) {
			$sub_all        = $summary_counts['publish'];
			$sub_has_en     = $summary_counts['pub_has_en'];
			$sub_has_es     = $summary_counts['pub_has_es'];
			$sub_missing_en = $summary_counts['pub_missing_en'];
			$sub_missing_es = $summary_counts['pub_missing_es'];
		} elseif ( $tab === 'draft' ) {
			$sub_all        = $summary_counts['draft'];
			$sub_has_en     = $summary_counts['draft_has_en'];
			$sub_has_es     = $summary_counts['draft_has_es'];
			$sub_missing_en = $summary_counts['draft_missing_en'];
			$sub_missing_es = $summary_counts['draft_missing_es'];
		} elseif ( $tab === 'trash' ) {
			$sub_all        = $summary_counts['trash'];
			$sub_has_en     = $summary_counts['trash_has_en'];
			$sub_has_es     = $summary_counts['trash_has_es'];
			$sub_missing_en = $summary_counts['trash_missing_en'];
			$sub_missing_es = $summary_counts['trash_missing_es'];
		} else { // 'all'
			$sub_all        = $summary_counts['all'];
			$sub_has_en     = $summary_counts['all_has_en'];
			$sub_has_es     = $summary_counts['all_has_es'];
			$sub_missing_en = $summary_counts['all_missing_en'];
			$sub_missing_es = $summary_counts['all_missing_es'];
		}

		// Base query args for preserving state across links
		$base_query_args = array();
		if ( ! empty( $s ) ) {
			$base_query_args['s'] = $s;
		}
		if ( ! empty( $orderby ) ) {
			$base_query_args['orderby'] = $orderby;
		}
		if ( ! empty( $order ) ) {
			$base_query_args['order'] = $order;
		}
		$sub_base_args = array_merge( $base_query_args, array( 'tab' => $tab, 'paged' => 1 ) );

		$sub_all_url        = add_query_arg( array_merge( $sub_base_args, array( 'subtab' => 'all' ) ), $current_url );
		$sub_has_en_url     = add_query_arg( array_merge( $sub_base_args, array( 'subtab' => 'has_en' ) ), $current_url );
		$sub_has_es_url     = add_query_arg( array_merge( $sub_base_args, array( 'subtab' => 'has_es' ) ), $current_url );
		$sub_missing_en_url = add_query_arg( array_merge( $sub_base_args, array( 'subtab' => 'missing_en' ) ), $current_url );
		$sub_missing_es_url = add_query_arg( array_merge( $sub_base_args, array( 'subtab' => 'missing_es' ) ), $current_url );
		?>
		<ul class="ptr-subsubsub">
			<li class="all">
				<a href="<?php echo esc_url( $sub_all_url ); ?>" class="<?php echo $subtab === 'all' ? 'current' : ''; ?>">
					<?php esc_html_e( 'All', 'property-translation-report' ); ?> <span class="count">(<?php echo esc_html( $sub_all ); ?>)</span>
				</a> |
			</li>
			<li class="has_en">
				<a href="<?php echo esc_url( $sub_has_en_url ); ?>" class="<?php echo $subtab === 'has_en' ? 'current' : ''; ?>">
					<?php esc_html_e( 'English', 'property-translation-report' ); ?> <span class="count">(<?php echo esc_html( $sub_has_en ); ?>)</span>
				</a> |
			</li>
			<li class="has_es">
				<a href="<?php echo esc_url( $sub_has_es_url ); ?>" class="<?php echo $subtab === 'has_es' ? 'current' : ''; ?>">
					<?php esc_html_e( 'Spanish', 'property-translation-report' ); ?> <span class="count">(<?php echo esc_html( $sub_has_es ); ?>)</span>
				</a> |
			</li>
			<li class="missing_en">
				<a href="<?php echo esc_url( $sub_missing_en_url ); ?>" class="<?php echo $subtab === 'missing_en' ? 'current' : ''; ?>" style="<?php echo $sub_missing_en > 0 ? 'color: #ef4444; font-weight: bold;' : ''; ?>">
					<?php esc_html_e( 'Missing English', 'property-translation-report' ); ?> <span class="count">(<?php echo esc_html( $sub_missing_en ); ?>)</span>
				</a> |
			</li>
			<li class="missing_es">
				<a href="<?php echo esc_url( $sub_missing_es_url ); ?>" class="<?php echo $subtab === 'missing_es' ? 'current' : ''; ?>" style="<?php echo $sub_missing_es > 0 ? 'color: #ef4444; font-weight: bold;' : ''; ?>">
					<?php esc_html_e( 'Missing Spanish', 'property-translation-report' ); ?> <span class="count">(<?php echo esc_html( $sub_missing_es ); ?>)</span>
				</a>
			</li>
		</ul>

		<!-- Search Form -->
		<form method="get" class="ptr-search-box">
			<input type="hidden" name="post_type" value="property">
			<input type="hidden" name="page" value="property-translation-report">
			<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>">
			<input type="hidden" name="subtab" value="<?php echo esc_attr( $subtab ); ?>">
			<input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>">
			<input type="hidden" name="order" value="<?php echo esc_attr( $order ); ?>">
			
			<p class="search-box">
				<label class="screen-reader-text" for="ptr-search-input"><?php esc_html_e( 'Search properties:', 'property-translation-report' ); ?></label>
				<input type="search" id="ptr-search-input" name="s" value="<?php echo esc_attr( $s ); ?>" placeholder="<?php esc_attr_e( 'Search ID or Title...', 'property-translation-report' ); ?>">
				<input type="submit" id="search-submit" class="button" value="<?php esc_attr_e( 'Search', 'property-translation-report' ); ?>">
			</p>
		</form>

		<div class="clear"></div>

		<!-- Table List Nav Top -->
		<div class="tablenav top">
			<div class="tablenav-pages">
				<span class="displaying-num"><?php echo esc_html( sprintf( _n( '%s item', '%s items', $total_items, 'property-translation-report' ), number_format_i18n( $total_items ) ) ); ?></span>
				<?php if ( $total_pages > 1 ) : ?>
					<span class="pagination-links">
						<?php if ( $paged > 1 ) : ?>
							<a class="first-page button" href="<?php echo esc_url( add_query_arg( 'paged', 1 ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'First page', 'property-translation-report' ); ?></span><span aria-hidden="true">«</span></a>
							<a class="prev-page button" href="<?php echo esc_url( add_query_arg( 'paged', $paged - 1 ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'property-translation-report' ); ?></span><span aria-hidden="true">‹</span></a>
						<?php else : ?>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
						<?php endif; ?>

						<span class="paging-input">
							<span class="screen-reader-text"><?php esc_html_e( 'Current Page', 'property-translation-report' ); ?></span>
							<span class="tablenav-paging-text"><?php echo esc_html( $paged ); ?> <?php esc_html_e( 'of', 'property-translation-report' ); ?> <span class="total-pages"><?php echo esc_html( $total_pages ); ?></span></span>
						</span>

						<?php if ( $paged < $total_pages ) : ?>
							<a class="next-page button" href="<?php echo esc_url( add_query_arg( 'paged', $paged + 1 ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Next page', 'property-translation-report' ); ?></span><span aria-hidden="true">›</span></a>
							<a class="last-page button" href="<?php echo esc_url( add_query_arg( 'paged', $total_pages ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Last page', 'property-translation-report' ); ?></span><span aria-hidden="true">»</span></a>
						<?php else : ?>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
						<?php endif; ?>
					</span>
				<?php endif; ?>
			</div>
			<div class="clear"></div>
		</div>

		<!-- Main Translation Comparison Table -->
		<table class="wp-list-table widefat fixed striped table-view-list posts">
			<thead>
				<tr>
					<th scope="col" class="column-primary" style="width: 50px;"><?php esc_html_e( '#', 'property-translation-report' ); ?></th>
					
					<!-- English Columns -->
					<th scope="col" class="ptr-debug-col manage-column"><?php esc_html_e( 'English WP ID', 'property-translation-report' ); ?></th>
					<?php ptr_render_sortable_header( 'english_property_id', __( 'English Property ID', 'property-translation-report' ), $orderby, $order ); ?>
					<?php ptr_render_sortable_header( 'english_title', __( 'English Name', 'property-translation-report' ), $orderby, $order ); ?>
					<?php ptr_render_sortable_header( 'english_status', __( 'English Status', 'property-translation-report' ), $orderby, $order ); ?>
					
					<!-- Spanish Columns -->
					<th scope="col" class="ptr-debug-col manage-column"><?php esc_html_e( 'Spanish WP ID', 'property-translation-report' ); ?></th>
					<?php ptr_render_sortable_header( 'spanish_property_id', __( 'Spanish Property ID', 'property-translation-report' ), $orderby, $order ); ?>
					<?php ptr_render_sortable_header( 'spanish_title', __( 'Spanish Name', 'property-translation-report' ), $orderby, $order ); ?>
					<?php ptr_render_sortable_header( 'spanish_status', __( 'Spanish Status', 'property-translation-report' ), $orderby, $order ); ?>
				</tr>
			</thead>
			
			<tbody>
				<?php if ( empty( $mapped_records ) ) : ?>
					<tr>
						<td colspan="9" style="text-align: center; padding: 20px 0;">
							<?php esc_html_e( 'No property records found matching current criteria.', 'property-translation-report' ); ?>
						</td>
					</tr>
				<?php else : ?>
					<?php 
					$row_index = ( $paged - 1 ) * $per_page + 1;
					foreach ( $mapped_records as $record ) : 
					?>
						<tr>
							<!-- Row Number -->
							<td class="column-primary">
								<strong><?php echo esc_html( $row_index++ ); ?></strong>
							</td>

							<!-- English WP Post ID (Debug) -->
							<td class="ptr-debug-col">
								<?php echo $record['en_id'] ? esc_html( $record['en_id'] ) : '<span class="ptr-text-muted">—</span>'; ?>
							</td>

							<!-- English Property ID (ACF property_id) -->
							<td>
								<?php 
								if ( $record['en_error'] === 'missing' ) {
									echo '<span class="ptr-badge ptr-badge-missing">' . esc_html__( 'Missing', 'property-translation-report' ) . '</span>';
								} elseif ( $record['en_error'] === 'orphan' ) {
									echo '<span class="ptr-badge ptr-badge-orphan">' . esc_html__( 'Orphan Translation', 'property-translation-report' ) . '</span>';
								} else {
									echo esc_html( $record['en_property_id'] ? $record['en_property_id'] : '—' );
								}
								?>
							</td>

							<!-- English Title (Edit Link) -->
							<td>
								<?php 
								if ( $record['en_error'] === 'missing' ) {
									echo '<span class="ptr-badge ptr-badge-missing">' . esc_html__( 'Missing', 'property-translation-report' ) . '</span>';
								} elseif ( $record['en_error'] === 'orphan' ) {
									echo '<span class="ptr-badge ptr-badge-orphan">' . esc_html__( 'Orphan Translation', 'property-translation-report' ) . '</span>';
								} else {
									$edit_link = get_edit_post_link( $record['en_id'] );
									if ( $edit_link ) {
										echo '<a href="' . esc_url( $edit_link ) . '"><strong>' . esc_html( $record['en_title'] ) . '</strong></a>';
									} else {
										echo esc_html( $record['en_title'] );
									}
								}
								?>
							</td>

							<!-- English Status -->
							<td>
								<?php 
								if ( $record['en_error'] === 'missing' ) {
									echo '<span class="ptr-badge ptr-badge-missing">' . esc_html__( 'Missing', 'property-translation-report' ) . '</span>';
								} elseif ( $record['en_error'] === 'orphan' ) {
									echo '<span class="ptr-badge ptr-badge-orphan">' . esc_html__( 'Orphan Translation', 'property-translation-report' ) . '</span>';
								} else {
									$is_en_status_mismatch = ( $tab !== 'all' && strtolower( $record['en_status'] ) !== strtolower( $tab ) );
									if ( $is_en_status_mismatch ) {
										echo '<span class="ptr-badge ptr-badge-missing" style="font-weight: bold; border-style: dashed;" title="' . esc_attr__( 'Status mismatch: English status does not match current tab filter.', 'property-translation-report' ) . '">' . esc_html( $record['en_status'] ) . '</span>';
									} else {
										echo esc_html( $record['en_status'] );
									}
								}
								?>
							</td>

							<!-- Spanish WP Post ID (Debug) -->
							<td class="ptr-debug-col">
								<?php echo $record['es_id'] ? esc_html( $record['es_id'] ) : '<span class="ptr-text-muted">—</span>'; ?>
							</td>

							<!-- Spanish Property ID (ACF property_id) -->
							<td>
								<?php 
								if ( $record['es_error'] === 'missing' ) {
									echo '<span class="ptr-badge ptr-badge-missing">' . esc_html__( 'Missing', 'property-translation-report' ) . '</span>';
								} elseif ( $record['es_error'] === 'orphan' ) {
									echo '<span class="ptr-badge ptr-badge-orphan">' . esc_html__( 'Orphan Translation', 'property-translation-report' ) . '</span>';
								} else {
									echo esc_html( $record['es_property_id'] ? $record['es_property_id'] : '—' );
								}
								?>
							</td>

							<!-- Spanish Title (Edit Link) -->
							<td>
								<?php 
								if ( $record['es_error'] === 'missing' ) {
									echo '<span class="ptr-badge ptr-badge-missing">' . esc_html__( 'Missing', 'property-translation-report' ) . '</span>';
								} elseif ( $record['es_error'] === 'orphan' ) {
									echo '<span class="ptr-badge ptr-badge-orphan">' . esc_html__( 'Orphan Translation', 'property-translation-report' ) . '</span>';
								} else {
									$edit_link = get_edit_post_link( $record['es_id'] );
									if ( $edit_link ) {
										echo '<a href="' . esc_url( $edit_link ) . '"><strong>' . esc_html( $record['es_title'] ) . '</strong></a>';
									} else {
										echo esc_html( $record['es_title'] );
									}
								}
								?>
							</td>

							<!-- Spanish Status -->
							<td>
								<?php 
								if ( $record['es_error'] === 'missing' ) {
									echo '<span class="ptr-badge ptr-badge-missing">' . esc_html__( 'Missing', 'property-translation-report' ) . '</span>';
								} elseif ( $record['es_error'] === 'orphan' ) {
									echo '<span class="ptr-badge ptr-badge-orphan">' . esc_html__( 'Orphan Translation', 'property-translation-report' ) . '</span>';
								} else {
									$is_es_status_mismatch = ( $tab !== 'all' && strtolower( $record['es_status'] ) !== strtolower( $tab ) );
									if ( $is_es_status_mismatch ) {
										echo '<span class="ptr-badge ptr-badge-missing" style="font-weight: bold; border-style: dashed;" title="' . esc_attr__( 'Status mismatch: Spanish status does not match current tab filter.', 'property-translation-report' ) . '">' . esc_html( $record['es_status'] ) . '</span>';
									} else {
										echo esc_html( $record['es_status'] );
									}
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<!-- Table List Nav Bottom -->
		<div class="tablenav bottom">
			<div class="tablenav-pages">
				<span class="displaying-num"><?php echo esc_html( sprintf( _n( '%s item', '%s items', $total_items, 'property-translation-report' ), number_format_i18n( $total_items ) ) ); ?></span>
				<?php if ( $total_pages > 1 ) : ?>
					<span class="pagination-links">
						<?php if ( $paged > 1 ) : ?>
							<a class="first-page button" href="<?php echo esc_url( add_query_arg( 'paged', 1 ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'First page', 'property-translation-report' ); ?></span><span aria-hidden="true">«</span></a>
							<a class="prev-page button" href="<?php echo esc_url( add_query_arg( 'paged', $paged - 1 ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'property-translation-report' ); ?></span><span aria-hidden="true">‹</span></a>
						<?php else : ?>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
						<?php endif; ?>

						<span class="paging-input">
							<span class="screen-reader-text"><?php esc_html_e( 'Current Page', 'property-translation-report' ); ?></span>
							<span class="tablenav-paging-text"><?php echo esc_html( $paged ); ?> <?php esc_html_e( 'of', 'property-translation-report' ); ?> <span class="total-pages"><?php echo esc_html( $total_pages ); ?></span></span>
						</span>

						<?php if ( $paged < $total_pages ) : ?>
							<a class="next-page button" href="<?php echo esc_url( add_query_arg( 'paged', $paged + 1 ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Next page', 'property-translation-report' ); ?></span><span aria-hidden="true">›</span></a>
							<a class="last-page button" href="<?php echo esc_url( add_query_arg( 'paged', $total_pages ) ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Last page', 'property-translation-report' ); ?></span><span aria-hidden="true">»</span></a>
						<?php else : ?>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
						<?php endif; ?>
					</span>
				<?php endif; ?>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<?php
}

/**
 * Render sortable column table headers.
 */
function ptr_render_sortable_header( $key, $label, $current_orderby, $current_order ) {
	$is_active = ( $current_orderby === $key );
	$next_order = ( $is_active && $current_order === 'asc' ) ? 'desc' : 'asc';
	
	$sort_url = add_query_arg( array(
		'orderby' => $key,
		'order'   => $next_order,
		'paged'   => 1,
	) );

	$classes = array( 'manage-column', 'sortable' );
	if ( $is_active ) {
		$classes[] = 'sorted';
		$classes[] = $current_order;
	} else {
		$classes[] = 'desc'; // default state helper
	}

	echo '<th scope="col" class="' . esc_attr( implode( ' ', $classes ) ) . '">';
	echo '<a href="' . esc_url( $sort_url ) . '">';
	echo '<span>' . esc_html( $label ) . '</span>';
	echo '<span class="sorting-indicator"></span>';
	echo '</a>';
	echo '</th>';
}
