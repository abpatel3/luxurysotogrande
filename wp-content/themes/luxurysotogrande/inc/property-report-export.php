<?php
/**
 * Property Translation Report Export Processor
 *
 * Handles Excel (.xlsx) downloads using PhpSpreadsheet and falls back to Excel-compatible Multi-Sheet XML.
 *
 * @package PropertyTranslationReport
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle export requests on admin_init.
 */
add_action( 'admin_init', 'ptr_handle_export_request' );
function ptr_handle_export_request() {
	// Verify action
	if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'export_property_report' ) {
		return;
	}

	// Verify Nonce
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'property_report_export_nonce' ) ) {
		wp_die( esc_html__( 'Security check failed. Please refresh the page and try again.', 'property-translation-report' ) );
	}

	// Check capabilities (similar to submenu registration)
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You do not have permission to export this report.', 'property-translation-report' ) );
	}

	// Verify WPML is active
	if ( ! function_exists( 'ptr_is_wpml_active' ) || ! ptr_is_wpml_active() ) {
		wp_die( esc_html__( 'WPML is active or table dependencies are missing. Export failed.', 'property-translation-report' ) );
	}

	// Parse parameters
	$tab     = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'all';
	$subtab  = isset( $_GET['subtab'] ) ? sanitize_key( $_GET['subtab'] ) : 'all';
	$s       = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
	$orderby = isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : 'english_title';
	$order   = isset( $_GET['order'] ) ? sanitize_key( $_GET['order'] ) : 'asc';

	// Try loading Composer autoloader from common locations to find PhpSpreadsheet
	$autoloader_paths = array(
		get_template_directory() . '/vendor/autoload.php',
		get_stylesheet_directory() . '/vendor/autoload.php',
		ABSPATH . 'vendor/autoload.php',
		dirname( __DIR__ ) . '/vendor/autoload.php',
	);

	foreach ( $autoloader_paths as $path ) {
		if ( file_exists( $path ) ) {
			require_once $path;
			break;
		}
	}

	// Check if PhpSpreadsheet is available
	if ( class_exists( '\PhpOffice\PhpSpreadsheet\Spreadsheet' ) ) {
		// True Excel XLSX Export
		ptr_execute_xlsx_export( $subtab, $s, $orderby, $order );
	} else {
		// Fallback to Multi-Sheet XML Excel Export (.xls) instead of simple CSV
		ptr_execute_spreadsheet_ml_fallback_export( $subtab, $s, $orderby, $order );
	}
}

/**
 * Execute XLSX export using PhpSpreadsheet.
 */
function ptr_execute_xlsx_export( $subtab, $s, $orderby, $order ) {
	$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

	// Sheet 1: Published
	$sheet_publish = $spreadsheet->getActiveSheet();
	$sheet_publish->setTitle( __( 'Published', 'property-translation-report' ) );
	$publish_ids = ptr_get_all_master_ids_for_export( array(
		'tab'     => 'publish',
		'subtab'  => $subtab,
		's'       => $s,
		'orderby' => $orderby,
		'order'   => $order,
	) );
	ptr_populate_worksheet( $sheet_publish, $publish_ids );

	// Sheet 2: Draft
	$sheet_draft = $spreadsheet->createSheet();
	$sheet_draft->setTitle( __( 'Draft', 'property-translation-report' ) );
	$draft_ids = ptr_get_all_master_ids_for_export( array(
		'tab'     => 'draft',
		'subtab'  => $subtab,
		's'       => $s,
		'orderby' => $orderby,
		'order'   => $order,
	) );
	ptr_populate_worksheet( $sheet_draft, $draft_ids );

	// Sheet 3: Trash
	$sheet_trash = $spreadsheet->createSheet();
	$sheet_trash->setTitle( __( 'Trash', 'property-translation-report' ) );
	$trash_ids = ptr_get_all_master_ids_for_export( array(
		'tab'     => 'trash',
		'subtab'  => $subtab,
		's'       => $s,
		'orderby' => $orderby,
		'order'   => $order,
	) );
	ptr_populate_worksheet( $sheet_trash, $trash_ids );

	// Reset active sheet to index 0 (Published)
	$spreadsheet->setActiveSheetIndex( 0 );

	// Clean output buffer before writing
	if ( ob_get_length() ) {
		ob_clean();
	}

	header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
	header( 'Content-Disposition: attachment; filename="property-translation-report-' . date( 'Y-m-d' ) . '.xlsx"' );
	header( 'Cache-Control: max-age=0' );
	header( 'Pragma: no-cache' );
	header( 'Expires: 0' );

	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx( $spreadsheet );
	$writer->save( 'php://output' );
	exit;
}

/**
 * Populate a given Excel Worksheet with properties data.
 */
function ptr_populate_worksheet( $sheet, $master_ids ) {
	// Write CSV headers row
	$sheet->setCellValue( 'A1', '#' );
	$sheet->setCellValue( 'B1', 'English WP ID' );
	$sheet->setCellValue( 'C1', 'English Property ID' );
	$sheet->setCellValue( 'D1', 'English Name' );
	$sheet->setCellValue( 'E1', 'English Status' );
	$sheet->setCellValue( 'F1', 'Spanish WP ID' );
	$sheet->setCellValue( 'G1', 'Spanish Property ID' );
	$sheet->setCellValue( 'H1', 'Spanish Name' );
	$sheet->setCellValue( 'I1', 'Spanish Status' );

	// Style headers (Bold text, Slate fill, Bottom border)
	$headerStyle = array(
		'font' => array(
			'bold'  => true,
			'color' => array( 'rgb' => '1E293B' ),
		),
		'fill' => array(
			'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
			'startColor' => array( 'rgb' => 'F1F5F9' ),
		),
		'borders' => array(
			'bottom' => array(
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
				'color'       => array( 'rgb' => 'CBD5E1' ),
			),
		),
	);
	$sheet->getStyle( 'A1:I1' )->applyFromArray( $headerStyle );

	$redStyle = array(
		'font' => array(
			'color' => array( 'rgb' => 'EF4444' ),
			'bold'  => true,
		),
	);

	$chunks = array_chunk( $master_ids, 100 );
	$rowNum = 2;
	$row_number = 1;

	foreach ( $chunks as $chunk ) {
		$master_query = new WP_Query( array(
			'post_type'        => 'property',
			'post__in'         => $chunk,
			'posts_per_page'   => -1,
			'post_status'      => array( 'publish', 'draft', 'trash', 'pending', 'future', 'private', 'inherit' ),
			'orderby'          => 'post__in',
			'suppress_filters' => true,
		) );

		if ( $master_query->have_posts() ) {
			while ( $master_query->have_posts() ) {
				$master_query->the_post();
				$master_post_id = get_the_ID();

				$trid = apply_filters( 'wpml_element_trid', null, $master_post_id, 'post_property' );
				if ( ! $trid ) {
					continue;
				}

				$translations = apply_filters( 'wpml_get_element_translations', null, $trid, 'post_property' );
				
				// Resolve English details
				$en_id = '';
				$en_property_id = '';
				$en_title = '';
				$en_status = '';
				$en_error = '';

				if ( isset( $translations['en'] ) ) {
					$en_id = (int) $translations['en']->element_id;
					$post_status = get_post_status( $en_id );
					if ( $post_status ) {
						$en_title = get_the_title( $en_id );
						$en_status = ucfirst( $post_status );
						$en_property_id = get_post_meta( $en_id, 'property_id', true );
					} else {
						$en_error = 'orphan';
						$en_property_id = __( 'Orphan Translation', 'property-translation-report' );
						$en_title       = __( 'Orphan Translation', 'property-translation-report' );
						$en_status      = __( 'Orphan Translation', 'property-translation-report' );
					}
				} else {
					$en_error = 'missing';
					$en_property_id = __( 'Missing', 'property-translation-report' );
					$en_title       = __( 'Missing', 'property-translation-report' );
					$en_status      = __( 'Missing', 'property-translation-report' );
				}

				// Resolve Spanish details
				$es_id = '';
				$es_property_id = '';
				$es_title = '';
				$es_status = '';
				$es_error = '';

				if ( isset( $translations['es'] ) ) {
					$es_id = (int) $translations['es']->element_id;
					$post_status = get_post_status( $es_id );
					if ( $post_status ) {
						$es_title = get_the_title( $es_id );
						$es_status = ucfirst( $post_status );
						$es_property_id = get_post_meta( $es_id, 'property_id', true );
					} else {
						$es_error = 'orphan';
						$es_property_id = __( 'Orphan Translation', 'property-translation-report' );
						$es_title       = __( 'Orphan Translation', 'property-translation-report' );
						$es_status      = __( 'Orphan Translation', 'property-translation-report' );
					}
				} else {
					$es_error = 'missing';
					$es_property_id = __( 'Missing', 'property-translation-report' );
					$es_title       = __( 'Missing', 'property-translation-report' );
					$es_status      = __( 'Missing', 'property-translation-report' );
				}

				// Write values to sheet
				$sheet->setCellValue( 'A' . $rowNum, $row_number++ );
				$sheet->setCellValue( 'B' . $rowNum, $en_id );
				$sheet->setCellValue( 'C' . $rowNum, $en_property_id );
				$sheet->setCellValue( 'D' . $rowNum, $en_title );
				$sheet->setCellValue( 'E' . $rowNum, $en_status );
				$sheet->setCellValue( 'F' . $rowNum, $es_id );
				$sheet->setCellValue( 'G' . $rowNum, $es_property_id );
				$sheet->setCellValue( 'H' . $rowNum, $es_title );
				$sheet->setCellValue( 'I' . $rowNum, $es_status );

				// Apply red color for errors
				if ( ! empty( $en_error ) ) {
					$sheet->getStyle( 'C' . $rowNum . ':E' . $rowNum )->applyFromArray( $redStyle );
				}
				if ( ! empty( $es_error ) ) {
					$sheet->getStyle( 'G' . $rowNum . ':I' . $rowNum )->applyFromArray( $redStyle );
				}

				$rowNum++;
			}
			wp_reset_postdata();
		}
	}

	// Auto-resize columns
	foreach ( range( 'A', 'I' ) as $col ) {
		$sheet->getColumnDimension( $col )->setAutoSize( true );
	}
}

/**
 * Execute SpreadsheetML Fallback Export (Downloads a true multi-sheet XML-based XLS).
 */
function ptr_execute_spreadsheet_ml_fallback_export( $subtab, $s, $orderby, $order ) {
	$publish_ids = ptr_get_all_master_ids_for_export( array(
		'tab'     => 'publish',
		'subtab'  => $subtab,
		's'       => $s,
		'orderby' => $orderby,
		'order'   => $order,
	) );
	$draft_ids = ptr_get_all_master_ids_for_export( array(
		'tab'     => 'draft',
		'subtab'  => $subtab,
		's'       => $s,
		'orderby' => $orderby,
		'order'   => $order,
	) );
	$trash_ids = ptr_get_all_master_ids_for_export( array(
		'tab'     => 'trash',
		'subtab'  => $subtab,
		's'       => $s,
		'orderby' => $orderby,
		'order'   => $order,
	) );

	$xml_publish = ptr_generate_sheet_rows_xml( $publish_ids );
	$xml_draft   = ptr_generate_sheet_rows_xml( $draft_ids );
	$xml_trash   = ptr_generate_sheet_rows_xml( $trash_ids );

	$xml = '<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:CharSet="1" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="Header">
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#1E293B" ss:Bold="1"/>
   <Interior ss:Color="#F1F5F9" ss:Pattern="Solid"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#CBD5E1"/>
   </Borders>
  </Style>
  <Style ss:ID="RedText">
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#EF4444" ss:Bold="1"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="' . esc_attr__( 'Published', 'property-translation-report' ) . '">
  <Table>
   <Column ss:Width="50"/>
   <Column ss:Width="100"/>
   <Column ss:Width="120"/>
   <Column ss:Width="250"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>
   <Column ss:Width="120"/>
   <Column ss:Width="250"/>
   <Column ss:Width="100"/>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">#</Data></Cell>
    <Cell><Data ss:Type="String">English WP ID</Data></Cell>
    <Cell><Data ss:Type="String">English Property ID</Data></Cell>
    <Cell><Data ss:Type="String">English Name</Data></Cell>
    <Cell><Data ss:Type="String">English Status</Data></Cell>
    <Cell><Data ss:Type="String">Spanish WP ID</Data></Cell>
    <Cell><Data ss:Type="String">Spanish Property ID</Data></Cell>
    <Cell><Data ss:Type="String">Spanish Name</Data></Cell>
    <Cell><Data ss:Type="String">Spanish Status</Data></Cell>
   </Row>
' . $xml_publish . '
  </Table>
 </Worksheet>
 <Worksheet ss:Name="' . esc_attr__( 'Draft', 'property-translation-report' ) . '">
  <Table>
   <Column ss:Width="50"/>
   <Column ss:Width="100"/>
   <Column ss:Width="120"/>
   <Column ss:Width="250"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>
   <Column ss:Width="120"/>
   <Column ss:Width="250"/>
   <Column ss:Width="100"/>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">#</Data></Cell>
    <Cell><Data ss:Type="String">English WP ID</Data></Cell>
    <Cell><Data ss:Type="String">English Property ID</Data></Cell>
    <Cell><Data ss:Type="String">English Name</Data></Cell>
    <Cell><Data ss:Type="String">English Status</Data></Cell>
    <Cell><Data ss:Type="String">Spanish WP ID</Data></Cell>
    <Cell><Data ss:Type="String">Spanish Property ID</Data></Cell>
    <Cell><Data ss:Type="String">Spanish Name</Data></Cell>
    <Cell><Data ss:Type="String">Spanish Status</Data></Cell>
   </Row>
' . $xml_draft . '
  </Table>
 </Worksheet>
 <Worksheet ss:Name="' . esc_attr__( 'Trash', 'property-translation-report' ) . '">
  <Table>
   <Column ss:Width="50"/>
   <Column ss:Width="100"/>
   <Column ss:Width="120"/>
   <Column ss:Width="250"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>
   <Column ss:Width="120"/>
   <Column ss:Width="250"/>
   <Column ss:Width="100"/>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">#</Data></Cell>
    <Cell><Data ss:Type="String">English WP ID</Data></Cell>
    <Cell><Data ss:Type="String">English Property ID</Data></Cell>
    <Cell><Data ss:Type="String">English Name</Data></Cell>
    <Cell><Data ss:Type="String">English Status</Data></Cell>
    <Cell><Data ss:Type="String">Spanish WP ID</Data></Cell>
    <Cell><Data ss:Type="String">Spanish Property ID</Data></Cell>
    <Cell><Data ss:Type="String">Spanish Name</Data></Cell>
    <Cell><Data ss:Type="String">Spanish Status</Data></Cell>
   </Row>
' . $xml_trash . '
  </Table>
 </Worksheet>
</Workbook>';

	// Clean output buffer before writing
	if ( ob_get_length() ) {
		ob_clean();
	}

	header( 'Content-Type: application/vnd.ms-excel; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="property-translation-report-' . date( 'Y-m-d' ) . '.xls"' );
	header( 'Cache-Control: max-age=0' );
	header( 'Pragma: no-cache' );
	header( 'Expires: 0' );

	echo $xml;
	exit;
}

/**
 * Generate XML Row structures for SpreadsheetML fallback.
 */
function ptr_generate_sheet_rows_xml( $master_ids ) {
	$xml = "";
	if ( empty( $master_ids ) ) {
		return $xml;
	}

	$chunks = array_chunk( $master_ids, 100 );
	$row_number = 1;

	foreach ( $chunks as $chunk ) {
		$master_query = new WP_Query( array(
			'post_type'        => 'property',
			'post__in'         => $chunk,
			'posts_per_page'   => -1,
			'post_status'      => array( 'publish', 'draft', 'trash', 'pending', 'future', 'private', 'inherit' ),
			'orderby'          => 'post__in',
			'suppress_filters' => true,
		) );

		if ( $master_query->have_posts() ) {
			while ( $master_query->have_posts() ) {
				$master_query->the_post();
				$master_post_id = get_the_ID();

				$trid = apply_filters( 'wpml_element_trid', null, $master_post_id, 'post_property' );
				if ( ! $trid ) {
					continue;
				}

				$translations = apply_filters( 'wpml_get_element_translations', null, $trid, 'post_property' );
				
				// English details
				$en_id = '';
				$en_property_id = '';
				$en_title = '';
				$en_status = '';
				$en_error = '';

				if ( isset( $translations['en'] ) ) {
					$en_id = (int) $translations['en']->element_id;
					$post_status = get_post_status( $en_id );
					if ( $post_status ) {
						$en_title = get_the_title( $en_id );
						$en_status = ucfirst( $post_status );
						$en_property_id = get_post_meta( $en_id, 'property_id', true );
					} else {
						$en_error = 'orphan';
						$en_property_id = __( 'Orphan Translation', 'property-translation-report' );
						$en_title       = __( 'Orphan Translation', 'property-translation-report' );
						$en_status      = __( 'Orphan Translation', 'property-translation-report' );
					}
				} else {
					$en_error = 'missing';
					$en_property_id = __( 'Missing', 'property-translation-report' );
					$en_title       = __( 'Missing', 'property-translation-report' );
					$en_status      = __( 'Missing', 'property-translation-report' );
				}

				// Spanish details
				$es_id = '';
				$es_property_id = '';
				$es_title = '';
				$es_status = '';
				$es_error = '';

				if ( isset( $translations['es'] ) ) {
					$es_id = (int) $translations['es']->element_id;
					$post_status = get_post_status( $es_id );
					if ( $post_status ) {
						$es_title = get_the_title( $es_id );
						$es_status = ucfirst( $post_status );
						$es_property_id = get_post_meta( $es_id, 'property_id', true );
					} else {
						$es_error = 'orphan';
						$es_property_id = __( 'Orphan Translation', 'property-translation-report' );
						$es_title       = __( 'Orphan Translation', 'property-translation-report' );
						$es_status      = __( 'Orphan Translation', 'property-translation-report' );
					}
				} else {
					$es_error = 'missing';
					$es_property_id = __( 'Missing', 'property-translation-report' );
					$es_title       = __( 'Missing', 'property-translation-report' );
					$es_status      = __( 'Missing', 'property-translation-report' );
				}

				// Escape variables for XML
				$en_id_esc          = htmlspecialchars( $en_id, ENT_QUOTES, 'UTF-8' );
				$en_property_id_esc = htmlspecialchars( $en_property_id, ENT_QUOTES, 'UTF-8' );
				$en_title_esc       = htmlspecialchars( $en_title, ENT_QUOTES, 'UTF-8' );
				$en_status_esc      = htmlspecialchars( $en_status, ENT_QUOTES, 'UTF-8' );

				$es_id_esc          = htmlspecialchars( $es_id, ENT_QUOTES, 'UTF-8' );
				$es_property_id_esc = htmlspecialchars( $es_property_id, ENT_QUOTES, 'UTF-8' );
				$es_title_esc       = htmlspecialchars( $es_title, ENT_QUOTES, 'UTF-8' );
				$es_status_esc      = htmlspecialchars( $es_status, ENT_QUOTES, 'UTF-8' );

				$en_style = ! empty( $en_error ) ? ' ss:StyleID="RedText"' : '';
				$es_style = ! empty( $es_error ) ? ' ss:StyleID="RedText"' : '';

				$xml .= "   <Row>\n";
				$xml .= "    <Cell><Data ss:Type=\"Number\">" . $row_number++ . "</Data></Cell>\n";
				$xml .= "    <Cell><Data ss:Type=\"String\">" . $en_id_esc . "</Data></Cell>\n";
				$xml .= "    <Cell" . $en_style . "><Data ss:Type=\"String\">" . $en_property_id_esc . "</Data></Cell>\n";
				$xml .= "    <Cell" . $en_style . "><Data ss:Type=\"String\">" . $en_title_esc . "</Data></Cell>\n";
				$xml .= "    <Cell" . $en_style . "><Data ss:Type=\"String\">" . $en_status_esc . "</Data></Cell>\n";
				$xml .= "    <Cell><Data ss:Type=\"String\">" . $es_id_esc . "</Data></Cell>\n";
				$xml .= "    <Cell" . $es_style . "><Data ss:Type=\"String\">" . $es_property_id_esc . "</Data></Cell>\n";
				$xml .= "    <Cell" . $es_style . "><Data ss:Type=\"String\">" . $es_title_esc . "</Data></Cell>\n";
				$xml .= "    <Cell" . $es_style . "><Data ss:Type=\"String\">" . $es_status_esc . "</Data></Cell>\n";
				$xml .= "   </Row>\n";
			}
			wp_reset_postdata();
		}
	}
	return $xml;
}

/**
 * Execute CSV export (Legacy, kept for reference).
 */
function ptr_execute_csv_export( $tab, $s, $orderby, $order ) {
	$query_args = array(
		'tab'     => $tab,
		's'       => $s,
		'orderby' => $orderby,
		'order'   => $order,
	);
	$master_ids = ptr_get_all_master_ids_for_export( $query_args );
	$chunks = array_chunk( $master_ids, 100 );

	// Clean output buffer before writing
	if ( ob_get_length() ) {
		ob_clean();
	}

	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="property-translation-report-' . sanitize_key( $tab ) . '-' . date( 'Y-m-d' ) . '.csv"' );
	header( 'Pragma: no-cache' );
	header( 'Expires: 0' );

	echo "\xEF\xBB\xBF";

	$output = fopen( 'php://output', 'w' );

	fputcsv( $output, array(
		'#',
		'English WP ID',
		'English Property ID',
		'English Name',
		'English Status',
		'Spanish WP ID',
		'Spanish Property ID',
		'Spanish Name',
		'Spanish Status'
	) );

	$row_number = 1;

	foreach ( $chunks as $chunk ) {
		$master_query = new WP_Query( array(
			'post_type'        => 'property',
			'post__in'         => $chunk,
			'posts_per_page'   => -1,
			'post_status'      => array( 'publish', 'draft', 'trash', 'pending', 'future', 'private', 'inherit' ),
			'orderby'          => 'post__in',
			'suppress_filters' => true,
		) );

		if ( $master_query->have_posts() ) {
			while ( $master_query->have_posts() ) {
				$master_query->the_post();
				$master_post_id = get_the_ID();

				$trid = apply_filters( 'wpml_element_trid', null, $master_post_id, 'post_property' );
				if ( ! $trid ) {
					continue;
				}

				$translations = apply_filters( 'wpml_get_element_translations', null, $trid, 'post_property' );
				
				$en_id = '';
				$en_property_id = '';
				$en_title = '';
				$en_status = '';

				if ( isset( $translations['en'] ) ) {
					$en_id = (int) $translations['en']->element_id;
					$post_status = get_post_status( $en_id );
					if ( $post_status ) {
						$en_title = get_the_title( $en_id );
						$en_status = ucfirst( $post_status );
						$en_property_id = get_post_meta( $en_id, 'property_id', true );
					} else {
						$en_property_id = __( 'Orphan Translation', 'property-translation-report' );
						$en_title       = __( 'Orphan Translation', 'property-translation-report' );
						$en_status      = __( 'Orphan Translation', 'property-translation-report' );
					}
				} else {
					$en_property_id = __( 'Missing', 'property-translation-report' );
					$en_title       = __( 'Missing', 'property-translation-report' );
					$en_status      = __( 'Missing', 'property-translation-report' );
				}

				$es_id = '';
				$es_property_id = '';
				$es_title = '';
				$es_status = '';

				if ( isset( $translations['es'] ) ) {
					$es_id = (int) $translations['es']->element_id;
					$post_status = get_post_status( $es_id );
					if ( $post_status ) {
						$es_title = get_the_title( $es_id );
						$es_status = ucfirst( $post_status );
						$es_property_id = get_post_meta( $es_id, 'property_id', true );
					} else {
						$es_property_id = __( 'Orphan Translation', 'property-translation-report' );
						$es_title       = __( 'Orphan Translation', 'property-translation-report' );
						$es_status      = __( 'Orphan Translation', 'property-translation-report' );
					}
				} else {
					$es_property_id = __( 'Missing', 'property-translation-report' );
					$es_title       = __( 'Missing', 'property-translation-report' );
					$es_status      = __( 'Missing', 'property-translation-report' );
				}

				fputcsv( $output, array(
					$row_number++,
					$en_id,
					$en_property_id,
					$en_title,
					$en_status,
					$es_id,
					$es_property_id,
					$es_title,
					$es_status
				) );
			}
			wp_reset_postdata();
		}
	}

	fclose( $output );
	exit;
}

/**
 * Fetch all matching master post IDs for export without pagination limits.
 */
function ptr_get_all_master_ids_for_export( $args ) {
	global $wpdb;

	$table_translations = $wpdb->prefix . 'icl_translations';
	$table_posts = $wpdb->posts;
	$table_postmeta = $wpdb->postmeta;

	$clauses = ptr_build_filter_clauses( $args );

	$query = "
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
	";

	return $wpdb->get_col( $query );
}
