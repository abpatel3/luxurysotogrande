<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

?>

<div class="wrap wpbs-wrap">

	<?php wpbs_get_wp_admin_header(); ?>

	<!-- Plugin Sticky Header -->
	<div class="wpbs-plugin-sticky-header">

		<!-- Page Title -->
		<span class="wpbs-page-heading"><?php echo __('Add-ons', 'wp-booking-system'); ?></span>

		<!-- Buttons -->
		<div class="wpbs-header-buttons">

		</div>

	</div>

	<!-- WP Notifications -->
	<div class="wpbs-notifications">
		<h1 class="wp-heading-inline"></h1>
	</div>

	<!-- Body -->
	<div class="wpbs-plugin-wrapper">
		<div class="wpbs-box wpbs-box-half">
			<form method="POST" action="">
				<div class="wpbs-box-header">
					<h2><?php echo __('Create export file', 'wp-booking-system'); ?></h2>
				</div>

				<div class="wpbs-box-content">

					<p><?php echo __('To export a backup file for WP Booking System, please click the Download Export File button.', 'wp-booking-system'); ?></p>
					<p><?php echo __('The export file will contain all information saved by the plugin, including calendars, bookings and general settings.', 'wp-booking-system'); ?></p>

				</div>

				<div class="wpbs-box-footer-wrapper">
					<div class="wpbs-box-footer">
						<input type="submit" class="wpbs-primary-button" value="<?php echo __('Download Export File', 'wp-booking-system'); ?>" />

						<!-- Nonce -->
						<?php wp_nonce_field('wpbs_backup_export', 'wpbs_token', false); ?>
						<input type="hidden" name="wpbs_action" value="backup_export" />

					</div>
				</div>

			</form>
		</div>

		<div class="wpbs-box wpbs-box-half">
			<form enctype="multipart/form-data" method="POST" action="">
				<div class="wpbs-box-header">
					<h2><?php echo __('Import data from file', 'wp-booking-system'); ?></h2>
				</div>

				<div class="wpbs-box-content">

					<p><?php echo __('To import all data from a WP Booking System export file, browse for that file and click the Import Data button.', 'wp-booking-system'); ?></p>
					<p><?php echo __('Please take into account that this action will firstly remove all data that you currently have in the plugin, then import the data from the export file.', 'wp-booking-system'); ?></p>
					<input type="file" name="import_file" />

				</div>

				<div class="wpbs-box-footer-wrapper">
					<div class="wpbs-box-footer">
						<input onclick="return confirm('<?php echo __('Are you sure you want to import data from the file?', 'wp-booking-system'); ?>' )" type="submit" class="wpbs-primary-button" value="<?php echo __('Import Data', 'wp-booking-system'); ?>" />

						<!-- Nonce -->
						<?php wp_nonce_field('wpbs_backup_import', 'wpbs_token', false); ?>
						<input type="hidden" name="wpbs_action" value="backup_import" />
					</div>
				</div>

			</form>
		</div>
	</div>
</div>