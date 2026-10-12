<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

?>

<div class="wrap wpbs-wrap wpbs-wrap-calendars">

	<?php wpbs_get_wp_admin_header(); ?>

	<!-- Plugin Sticky Header -->
	<div class="wpbs-plugin-sticky-header">

		<!-- Page Title -->
		<span class="wpbs-page-heading"><?php echo __('Calendars', 'wp-booking-system'); ?></span>

		<!-- Buttons -->
		<div class="wpbs-header-buttons">
			<a href="<?php echo add_query_arg(array('subpage' => 'add-calendar'), $this->admin_url); ?>" class="wpbs-primary-button"><?php echo __('Add New Calendar', 'wp-booking-system'); ?></a>
		</div>

	</div>

	<!-- WP Notifications -->
	<div class="wpbs-notifications">
		<h1 class="wp-heading-inline"></h1>
	</div>

	<!-- Body -->
	<div class="wpbs-plugin-wrapper">
		<div class="wpbs-box wpbs-transparent">

			<!-- Calendars List Table -->
			<form method="get">


				<div class="wpbs-box-header wpbs-box-toolbar">
					<?php $table = new WPBS_WP_List_Table_Calendars(); ?>
					<?php $table->views(); ?>
					<?php $table->search_box(__('Search Calendars', 'wp-booking-system'), 'wpbs-search-calendars'); ?>
				</div>

				<div class="wpbs-box-content wpbs-no-padding">
					<div class="wpbs-box-table-wrapper">

						<input type="hidden" name="page" value="wpbs-calendars" />
						<input type="hidden" name="paged" value="1">

						<?php
						$table = new WPBS_WP_List_Table_Calendars();

						if (function_exists('wpbs_get_calendar_categories') && wpbs_get_calendar_categories()) {

							$uncategorised = __('Uncategorised', 'wp-booking-system');

							echo '<div class="wpbs-categorised-calendars">';
							global $wpdb;

							$sql = $wpdb->prepare(
								"
									SELECT 
										COALESCE(NULLIF(cm.meta_value, ''), %s) AS category,
										GROUP_CONCAT(DISTINCT c.id ORDER BY c.id ASC) AS calendar_ids
									FROM {$wpdb->prefix}wpbs_calendars c
									LEFT JOIN {$wpdb->prefix}wpbs_calendar_meta cm
										ON cm.calendar_id = c.id
									AND cm.meta_key = %s
									GROUP BY category
									",
								$uncategorised,
								'calendar_category'
							);

							$rows = $wpdb->get_results($sql, ARRAY_A);

							$calendar_ids_by_category = array();

							foreach ($rows as $row) {
								$calendar_ids_by_category[$row['category']] = array_map('intval', explode(',', $row['calendar_ids']));
							}

							$i = 1;
							foreach ($calendar_ids_by_category as $category => $calendar_ids) {
								if (!in_array($category, wpbs_get_calendar_categories()) && $category != $uncategorised) {
									continue;
								}

								echo '<h2>' . $category . '</h2>';
								echo '<div class="wpbs-categorised-calendar">';
								$table = new WPBS_WP_List_Table_Calendars($calendar_ids, $i++);
								$table->display();
								echo '</div>';
							}

							echo '</div>';

						} else {
							$table->display();
						}
						?>

					</div>
				</div>

			</form>

		</div>
	</div>

</div>