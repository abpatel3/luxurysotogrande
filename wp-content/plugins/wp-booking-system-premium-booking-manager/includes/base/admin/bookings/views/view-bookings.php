<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

$view = wpbs_bm_get_dashboard_view();

?>

<div class="wrap wpbs-wrap wpbs-wrap-bookings">

	<?php wpbs_get_wp_admin_header(); ?>

	<!-- Plugin Sticky Header -->
	<div class="wpbs-plugin-sticky-header">

		<!-- Page Title -->
		<span class="wpbs-page-heading"><?php echo __('Bookings Manager', 'wp-booking-system-booking-manager'); ?></span>

		<!-- Buttons -->
		<div class="wpbs-header-buttons">
			<div class="view-switch media-grid-view-switch wpbs-bm-view-switch">
				<a href="<?php echo add_query_arg(array('page' => 'wpbs-bookings', 'view' => 'list'), admin_url('admin.php')); ?>" class="view-list <?php echo $view == 'list' ? 'current' : ''; ?>" title="<?php _e('List view', 'wp-booking-system-booking-manager') ?>">
					<?php _e('List view', 'wp-booking-system-booking-manager') ?>
				</a>
				<a href="<?php echo add_query_arg(array('page' => 'wpbs-bookings', 'view' => 'calendar'), admin_url('admin.php')); ?>" class="view-grid <?php echo $view == 'calendar' ? 'current' : ''; ?>" title="<?php _e('Calendar view', 'wp-booking-system-booking-manager') ?>">
					<?php _e('Calendar view', 'wp-booking-system-booking-manager') ?>
				</a>
			</div>
		</div>

	</div>

	<!-- WP Notifications -->
	<div class="wpbs-notifications">
		<h1 class="wp-heading-inline"></h1>
	</div>

	<!-- Body -->
	<div class="wpbs-plugin-wrapper">

		<div class="wpbs-box wpbs-transparent wpbs-no-overflow">

			<?php if ($view == 'list') : ?>

				<!-- Bookings List Table -->
				<form method="get" autocomplete="off">

					<input type="hidden" name="page" value="wpbs-bookings" />
					<input type="hidden" name="paged" value="1">

					<div class="wpbs-box-header wpbs-box-toolbar">
						<?php
						$table = new WPBS_WP_List_Table_Bookings();
						$table->views();
						$table->search_box(__('Search Bookings', 'wp-booking-system-booking-manager'), 'wpbs-search-bookings');
						?>
					</div>

					<div class="wpbs-box-content wpbs-no-padding">
						<div class="wpbs-box-table-wrapper">
							<?php $table->display(); ?>
						</div>
					</div>
				</form>

			<?php else : ?>
				<!-- Bookings Calendar View -->
				<?php
				if (function_exists('wpbs_get_calendar_categories') && wpbs_get_calendar_categories()) {
					$calendars = wpbs_get_calendars();
					$uncategorised = __('Uncategorised', 'wp-booking-system');
					foreach ($calendars as $calendar) {
						$category = wpbs_get_calendar_category($calendar->get('id')) ?: $uncategorised;
						$categories[$category][] = $calendar->get('id');
					}

					foreach (array_merge(wpbs_get_calendar_categories(), [$uncategorised]) as $category) {
						if (isset($categories[$category])) {
							echo '<h2>' . $category . '</h2>';
							$calendar = new WPBS_BM_Calendar_View_Bookings($categories[$category]);
							$calendar->display();
						}
					}
				} else {
					$calendar = new WPBS_BM_Calendar_View_Bookings();
					$calendar->display();
				}

				?>

			<?php endif; ?>
		</div>

		<?php do_action('wpbs_booking_manager_after_list_table') ?>

	</div>

</div>

<div id="wpbs-placeholder-editor">
	<?php wp_editor('', 'wpbs_placeholder_editor', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false)); ?>
</div>