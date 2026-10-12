<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

?>

<div class="wpbs-tab-inner">

	<?php

	/**
	 * Hook to add extra fields at the top of the General Tab
	 *
	 * @param array $settings
	 *
	 */
	do_action('wpbs_submenu_page_settings_tab_general_top', $settings);

	?>

	<h2><?php echo __('General Settings', 'wp-booking-system') ?></h2>

	<!-- Booking History Color -->
	<div class="wpbs-form-field">

		<label class="wpbs-label"><?php echo __('Booking History Color', 'wp-booking-system'); ?></label>

		<div class="wpbs-input">
			<input name="wpbs_settings[booking_history_color]" type="text" class="wpbs-colorpicker" value="<?php echo (! empty($settings['booking_history_color']) ? esc_attr($settings['booking_history_color']) : ''); ?>" />
		</div>

		<div class="wpbs-label-description">
			<?php echo __('The color to be applied to past calendar dates if the "History" option is enabled in the embed options.', 'wp-booking-system'); ?>
		</div>

	</div>

	<!-- Booking Selection Hover -->
	<div class="wpbs-form-field">

		<label class="wpbs-label"><?php echo __('Booking Hover Color', 'wp-booking-system'); ?></label>

		<div class="wpbs-input">
			<input name="wpbs_settings[booking_selection_hover_color]" type="text" class="wpbs-colorpicker" value="<?php echo (! empty($settings['booking_selection_hover_color']) ? esc_attr($settings['booking_selection_hover_color']) : ''); ?>" />
		</div>

		<div class="wpbs-label-description">
			<?php echo __('The color of the calendar dates when hovering over them.', 'wp-booking-system'); ?>
		</div>

	</div>

	<!-- Booking Selection Hover -->
	<div class="wpbs-form-field">

		<label class="wpbs-label"><?php echo __('Booking Selection Color', 'wp-booking-system'); ?></label>

		<div class="wpbs-input">
			<input name="wpbs_settings[booking_selection_selected_color]" type="text" class="wpbs-colorpicker" value="<?php echo (! empty($settings['booking_selection_selected_color']) ? esc_attr($settings['booking_selection_selected_color']) : ''); ?>" />
		</div>

		<div class="wpbs-label-description">
			<?php echo __('The color of the calendar dates after they have been selected.', 'wp-booking-system'); ?>
		</div>

	</div>

	<!-- Current Day Highlight Color -->
	<div class="wpbs-form-field">

		<label class="wpbs-label"><?php echo __("Today's Highlight Color", 'wp-booking-system'); ?></label>

		<div class="wpbs-input">
			<input name="wpbs_settings[today_highlight_color]" type="text" class="wpbs-colorpicker" value="<?php echo (! empty($settings['today_highlight_color']) ? esc_attr($settings['today_highlight_color']) : ''); ?>" />
		</div>

		<div class="wpbs-label-description">
			<?php echo __('The border highlight color of the current day.', 'wp-booking-system'); ?>
		</div>

	</div>


	<!-- Calendar Back-end Start Day -->
	<div class="wpbs-form-field">

		<label class="wpbs-label"><?php echo __('Backend Start Day', 'wp-booking-system'); ?></label>

		<div class="wpbs-input">
			<select name="wpbs_settings[backend_start_day]">
				<?php

				$weekday = wpbs_get_translated_weekdays();

				foreach ($weekday as $key => $day_name) {

					// Weekdays keys start at 1, not 0
					$key++;

					echo '<option value="' . esc_attr($key) . '" ' . (! empty($settings['backend_start_day']) ? selected($settings['backend_start_day'], $key, false) : '') . '>' . $day_name . '</option>';
				}

				?>
			</select>
		</div>

	</div>


	<!-- iCal Refresh Times -->
	<div id="wpbs-settings-field-ical-refresh-times-wrapper" class="wpbs-form-field">

		<label class="wpbs-label"><?php echo __('iCal Refresh Times', 'wp-booking-system'); ?></label>

		<div class="wpbs-input">
			<select name="wpbs_settings[ical_refresh_times]">
				<?php $selected = (! empty($settings['ical_refresh_times']) ? $settings['ical_refresh_times'] : 'hourly'); ?>
				<option value="page_load" <?php selected($selected, 'page_load'); ?>><?php echo __('On each page load', 'wp-booking-system'); ?></option>
				<option value="hourly" <?php selected($selected, 'hourly'); ?>><?php echo __('Hourly', 'wp-booking-system'); ?></option>
				<option value="daily" <?php selected($selected, 'daily'); ?>><?php echo __('Daily', 'wp-booking-system'); ?></option>
				<option value="custom" <?php selected($selected, 'custom'); ?>><?php echo __('Custom', 'wp-booking-system'); ?></option>
			</select>
		</div>

	</div>

	<!-- Custom iCal Refresh Times -->
	<div id="wpbs-settings-field-ical-custom-refresh-time-wrapper" class="wpbs-form-field">

		<label class="wpbs-label"><?php echo __('iCal Custom Refresh Time', 'wp-booking-system'); ?></label>

		<div class="wpbs-input wpbs-has-columns">

			<div class="wpbs-half">
				<input name="wpbs_settings[ical_custom_refresh_time]" type="number" value="<?php echo (! empty($settings['ical_custom_refresh_time']) ? esc_attr($settings['ical_custom_refresh_time']) : ''); ?>" />
			</div>

			<div class="wpbs-half">
				<select name="wpbs_settings[ical_custom_refresh_time_unit]">
					<?php $selected = (! empty($settings['ical_custom_refresh_time_unit']) ? $settings['ical_custom_refresh_time_unit'] : 'page_load'); ?>
					<option value="minutes" <?php selected($selected, 'minutes'); ?>><?php echo __('Minutes', 'wp-booking-system'); ?></option>
					<option value="hours" <?php selected($selected, 'hours'); ?>><?php echo __('Hours', 'wp-booking-system'); ?></option>
				</select>
			</div>

		</div>

	</div>


	<!-- Enable Improve Page Load Speed -->
	<div class="wpbs-form-field">
		<label class="wpbs-label" for="custom_enqueue_scripts"><?php echo __('Improve Page Load Speed', 'wp-booking-system'); ?></label>

		<div class="wpbs-input">
			<label for="custom_enqueue_scripts" class="wpbs-checkbox-switch">
				<input type="hidden" name="wpbs_settings[custom_enqueue_scripts]" value="0">
				<input data-target="#wpbs-page-load-speed" name="wpbs_settings[custom_enqueue_scripts]" type="checkbox" id="custom_enqueue_scripts" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($settings['custom_enqueue_scripts'])) ? 'checked' : ''; ?>>
				<div class="wpbs-checkbox-slider"></div>
			</label>
		</div>

		<div class="wpbs-label-description"><?php echo __('Load the required calendar script and styles only on specific pages.', 'wp-booking-system'); ?></div>
	</div>

	<div id="wpbs-page-load-speed" class="wpbs-form-fields-indent wpbs-settings-wrapper <?php echo (!empty($settings['custom_enqueue_scripts'])) ? 'wpbs-settings-wrapper-show' : ''; ?>">



		<?php $post_types_dropdown = wpbs_get_post_types_as_dropdown(); ?>

		<!-- Pages -->
		<div class="wpbs-form-field">

			<label class="wpbs-label">
				<?php echo __('Pages to enqueue scripts on', 'wp-booking-system'); ?>
			</label>

			<div class="wpbs-input wpbs-chosen-wrapper">

				<select multiple name="wpbs_settings[custom_enqueue_scripts_pages][]" class="wpbs-chosen">
					<option value=""><?php echo __('Select...', 'wp-booking-system'); ?></option>
					<?php foreach ($post_types_dropdown as $post_type => $posts): ?>
						<optgroup label="<?php echo $post_type; ?>">
							<?php foreach ($posts as $post_id => $post_title): ?>
								<option value="<?php echo $post_id; ?>" <?php echo isset($settings['custom_enqueue_scripts_pages']) && in_array($post_id, $settings['custom_enqueue_scripts_pages']) ? 'selected' : ''; ?>><?php echo $post_title; ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>

			</div>



		</div>

		<div class="wpbs-page-notice notice-info">
			<p><?php echo __('By default, the scripts and styles required by the plugin are loaded on all pages. By enabling this option, the scripts and styles will only be enqueued on the pages you select below. Please note that this option may not work well on websites that use AJAX to navigate between pages.', 'wp-booking-system'); ?></p>
		</div>

	</div>


	<?php

	/**
	 * Hook to add extra fields at the bottom of the General Tab
	 *
	 * @param array $settings
	 *
	 */
	do_action('wpbs_submenu_page_settings_tab_general_bottom', $settings);

	?>

</div>

<!-- Save Page Button -->
<div class="wpbs-box-footer-wrapper">
	<div class="wpbs-box-footer">
		<input type="submit" class="wpbs-primary-button" value="<?php echo __('Save Settings', 'wp-booking-system'); ?>" />
	</div>
</div>