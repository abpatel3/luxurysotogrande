<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$calendar_id = absint(! empty($_GET['calendar_id']) ? $_GET['calendar_id'] : 0);

?>

<div class="wrap wpbs-wrap wpbs-wrap-legend-items">

	<?php wpbs_get_wp_admin_header(); ?>

	<!-- Plugin Sticky Header -->
	<div class="wpbs-plugin-sticky-header">

		<!-- Page Title -->
		<span class="wpbs-page-heading">
			<?php echo __('Calendar Legend', 'wp-booking-system'); ?>
			<span class="wpbs-heading-tag"><?php printf(__('Calendar ID: %d', 'wp-booking-system'), $calendar_id); ?></span>
		</span>

		<!-- Buttons -->
		<div class="wpbs-header-buttons">
			<a href="<?php echo add_query_arg(array('subpage' => 'edit-calendar')); ?>" class="wpbs-secondary-button"><?php echo __('Back to Calendar', 'wp-booking-system'); ?></a>
			<a href="<?php echo add_query_arg(array('subpage' => 'add-legend-item', 'calendar_id' => $calendar_id), $this->admin_url); ?>" class="wpbs-primary-button"><?php echo __('Add New Legend Item', 'wp-booking-system'); ?></a>
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
			<div class="wpbs-box-content wpbs-no-padding">
				<div class="wpbs-box-table-wrapper">
					<?php
					$table = new WPBS_WP_List_Table_Legend_Items();
					$table->display();
					?>
				</div>
			</div>


			<style>
				<?php
				/**
				 * Legend Items CSS
				 *
				 */
				foreach (wpbs_get_legend_items(array('calendar_id' => $calendar_id)) as $legend_item):
					// Background colors
					$colors = $legend_item->get('color');
				?>
				.wpbs-legend-item-icon-<?php echo esc_attr($legend_item->get('id')); ?> div:first-of-type {
					background-color: <?php echo (!empty($colors[0]) ? esc_attr($colors[0]) : 'transparent'); ?>;
				}

				.wpbs-legend-item-icon-<?php echo esc_attr($legend_item->get('id')); ?> div:nth-of-type(2) {
					background-color: <?php echo (!empty($colors[1]) ? esc_attr($colors[1]) : 'transparent'); ?>;
				}

				.wpbs-legend-item-icon-<?php echo esc_attr($legend_item->get('id')); ?> div:first-of-type svg {
					fill: <?php echo (! empty($colors[0]) ? esc_attr($colors[0]) : 'transparent'); ?> !important;
				}

				.wpbs-legend-item-icon-<?php echo esc_attr($legend_item->get('id')); ?> div:nth-of-type(2) svg {
					fill: <?php echo (! empty($colors[1]) ? esc_attr($colors[1]) : 'transparent'); ?> !important;
				}

				<?php endforeach; ?>
			</style>

		</div>
	</div>
</div>