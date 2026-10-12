<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$calendars = wpbs_get_calendars(array('status' => 'active'));

?>

<div class="wrap wpbs-wrap wpbs-wrap-add-calendar">

	<?php wpbs_get_wp_admin_header(); ?>

	<!-- Plugin Sticky Header -->
	<div class="wpbs-plugin-sticky-header">

		<!-- Page Title -->
		<span class="wpbs-page-heading"><?php echo __('Add New Calendar', 'wp-booking-system'); ?></span>

		<!-- Buttons -->
		<div class="wpbs-header-buttons">
			<a href="<?php echo add_query_arg(array('page' => 'wpbs-calendars'), admin_url('admin.php')); ?>" class="wpbs-secondary-button"><?php echo __('Back to all calendars', 'wp-booking-system') ?></a>
		</div>

	</div>

	<!-- WP Notifications -->
	<div class="wpbs-notifications">
		<h1 class="wp-heading-inline"></h1>
	</div>

	<div class="wpbs-plugin-wrapper">
		<div class="wpbs-box wpbs-box-half wpbs-box-centered">
			<form action="" method="POST">

				<div class="wpbs-box-header">
					<h2>
						<div class="wpbs-add-icon">
							<span class="dashicons dashicons-calendar-alt"></span>
						</div><?php echo __('Add New Calendar', 'wp-booking-system'); ?>
					</h2>
				</div>

				<div class="wpbs-box-content">

					<!-- Add Calendar Name -->
					<div class="wpbs-form-field">
						<label class="wpbs-label" for="wpbs-new-calendar-name"><?php echo __('Calendar Name', 'wp-booking-system'); ?></label>
						<div class="wpbs-input">
							<input id="wpbs-new-calendar-name" name="calendar_name" type="text" value="<?php echo (! empty($_POST['calendar_name']) ? esc_attr($_POST['calendar_name']) : ''); ?>" />
						</div>
					</div>

					<?php if (wpbs_is_pricing_enabled()): ?>
						<div class="wpbs-form-field">
							<label class="wpbs-label" for="wpbs-new-calendar-price"><?php echo __('Default price', 'wp-booking-system'); ?></label>
							<div class="wpbs-input">
								<div class="wpbs-input-wrapper">
									<span class="wpbs-input-symbol wpbs-input-symbol-left"><span><?php echo wpbs_get_currency() ?></span></span>
									<input id="wpbs-new-calendar-price" name="calendar_price" type="number" value="<?php echo (! empty($_POST['calendar_price']) ? esc_attr($_POST['calendar_price']) : '0'); ?>" />
								</div>
							</div>
						</div>
					<?php endif; ?>

					<!-- Select Calendar Legend -->
					<?php if (! empty($calendars)): ?>

						<div class="wpbs-form-field">
							<label class="wpbs-label" for="wpbs-new-calendar-legend"><?php echo __('Copy Legend', 'wp-booking-system'); ?></label>
							<div class="wpbs-input">
								<select id="wpbs-new-calendar-legend" name="calendar_legend">
									<option value=""><?php echo __('Select calendar...', 'wp-booking-system'); ?></option>
									<?php if (! empty($calendars)): ?>
										<?php foreach ($calendars as $calendar): ?>
											<option value="<?php echo $calendar->get('id'); ?>"><?php printf(__('Copy from calendar #%d (%s)', 'wp-booking-system'), $calendar->get('id'), $calendar->get('name')); ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
							<div class="wpbs-label-description"><?php echo __("If you wish to copy the legend items from one calendar to this new one, select from which calendar from the drop-down below.", 'wp-booking-system'); ?></div>
						</div>

					<?php endif; ?>

				</div>

				<div class="wpbs-box-footer-wrapper">
					<div class="wpbs-box-footer">
						<div class="wpbs-spaced-buttons">
							<input type="submit" class="wpbs-primary-button" value="<?php echo __('Add Calendar', 'wp-booking-system'); ?>" />
							<a class="wpbs-secondary-button wpbs-remove-button" href="<?php echo admin_url($this->admin_url); ?>"><?php echo __('Cancel', 'wp-booking-system'); ?></a>
						</div>

						<!-- Action and nonce -->
						<input type="hidden" name="wpbs_action" value="add_calendar" />
						<?php wp_nonce_field('wpbs_add_calendar', 'wpbs_token', false); ?>

					</div>
				</div>

			</form>

		</div>
	</div>

</div>