<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$calendar_id = (! empty($_GET['calendar_id']) ? absint($_GET['calendar_id']) : 0);
$calendar    = wpbs_get_calendar($calendar_id);

$legend_items 		 = wpbs_get_legend_items(array('calendar_id' => $calendar_id));
$export_legend_items = wpbs_get_calendar_meta($calendar_id, 'ical_export_legend_items', true);
$group_events_by_description = wpbs_get_calendar_meta($calendar_id, 'group_events_by_description', true);

if (empty($export_legend_items))
	$export_legend_items = array();

$ical_status = wpbs_get_calendar_meta($calendar_id, 'disable_icalendar_links', true);

?>

<div class="wrap wpbs-wrap">

	<?php wpbs_get_wp_admin_header(); ?>

	<!-- Plugin Sticky Header -->
	<div class="wpbs-plugin-sticky-header">

		<!-- Page Title -->
		<span class="wpbs-page-heading">
			<?php echo __('iCal Import/Export', 'wp-booking-system'); ?>
			<span class="wpbs-heading-tag"><?php printf(__('Calendar ID: %d', 'wp-booking-system'), $calendar_id); ?></span>
		</span>

		<!-- Buttons -->
		<div class="wpbs-header-buttons">
			<a href="<?php echo add_query_arg(array('subpage' => 'edit-calendar')); ?>" class="wpbs-secondary-button"><?php echo __('Back to Calendar', 'wp-booking-system'); ?></a>
		</div>

	</div>

	<!-- WP Notifications -->
	<div class="wpbs-notifications">
		<h1 class="wp-heading-inline"></h1>
	</div>



	<!-- Body -->
	<div class="wpbs-plugin-wrapper">

		<div class="wpbs-box">

			<form method="POST" action="">

				<div class="wpbs-box-header wpbs-has-buttons">
					<h2><?php echo __('Export', 'wp-booking-system'); ?></h2>
					<div class="wpbs-buttons">
						<a class="wpbs-secondary-button" onclick="return confirm('<?php echo __('Are you sure you want to reset the iCalendar feed link for this calendar?', 'wp-booking-system'); ?>' )" href="<?php echo (wp_nonce_url(add_query_arg(array('wpbs_action' => 'reset_private_link'), remove_query_arg('wpbs_message')), 'wpbs_reset_private_link', 'wpbs_token')); ?>"><?php echo __('Reset Private Link', 'wp-booking-system'); ?></a>
					</div>
				</div>

				<!-- Form Fields -->
				<div class="wpbs-box-content">

					<!-- iCal Feed Link -->
					<div class="wpbs-form-field">

						<label class="wpbs-label"><?php echo __('iCalendar Link', 'wp-booking-system'); ?></label>

						<div class="wpbs-input">
							<input id="wpbs-settings-field-ical-export" readonly type="text" value="<?php echo add_query_arg(array('wpbs-ical' => $calendar->get('ical_hash')), site_url() . '/'); ?>.ics" />
						</div>

					</div>

					<!-- Export as Booked Legend Items -->
					<div class="wpbs-form-field">

						<label class="wpbs-label"><?php echo __('Legend Items to Export as Booked', 'wp-booking-system'); ?></label>

						<div class="wpbs-input wpbs-chosen-wrapper">

							<select multiple class="wpbs-chosen" name="ical_export_legend_items[]" id="ical-export-legend-items">
								<?php
								foreach ($legend_items as $legend_item) {
									echo '<option value="' . esc_attr($legend_item->get('id')) . '" ' . (in_array($legend_item->get('id'), $export_legend_items) ? 'selected' : '') . '>' . $legend_item->get('name') . '</option>';
								}
								?>
							</select>

						</div>

					</div>

					<div class="wpbs-ical-warning wpbs-page-notice notice-warning">
						<?php echo __('When exporting changeover days, do not include the ending changeover in the list above.', 'wp-booking-system') ?>
					</div>

					<!-- Group events by description -->
					<div class="wpbs-form-field">

						<label class="wpbs-label">
							<?php echo __('Group Events by Description', 'wp-booking-system'); ?>

						</label>

						<div class="wpbs-input">

							<label for="group_events_by_description" class="wpbs-checkbox-switch">
								<input type="hidden" name="group_events_by_description" value="0">
								<input type="checkbox" id="group_events_by_description" name="group_events_by_description" value="1" class="wpbs-settings-toggle" <?php checked('1', $group_events_by_description); ?>>
								<div class="wpbs-checkbox-slider"></div>
							</label>


						</div>

						<div class="wpbs-label-description">
							<?php echo __('If selected, events that have the same description will be grouped into one event spaning over multiple days.', 'wp-booking-system'); ?>
						</div>

					</div>

				</div>

				<div class="wpbs-box-footer-wrapper">
					<div class="wpbs-box-footer">

						<input type="submit" class="wpbs-primary-button" value="<?php echo __('Save Preferences', 'wp-booking-system'); ?>" />

						<!-- Calendar ID -->
						<input type="hidden" name="calendar_id" value="<?php echo $calendar_id; ?>" />

						<!-- Nonce -->
						<?php wp_nonce_field('wpbs_ical_export_save_preferences', 'wpbs_token', false); ?>
						<input type="hidden" name="wpbs_action" value="ical_export_save_preferences" />

					</div>
				</div>

			</form>

		</div>


		<div class="wpbs-box wpbs-box-half">

			<form id="wpbs-ical-file-import" enctype="multipart/form-data" method="POST" action="">

				<div class="wpbs-box-header">
					<h2><?php echo __('Import from File', 'wp-booking-system'); ?></h2>
				</div>

				<div class="wpbs-box-content">

					<p><?php echo __('Importing from a .ics file will insert the iCal events into the calendar. You will then be able to edit the details for each imported date.', 'wp-booking-system'); ?></p>


					<!-- iCal File -->
					<div class="wpbs-form-field">

						<label class="wpbs-label"><?php echo __('iCalendar File', 'wp-booking-system'); ?> *</label>

						<div class="wpbs-input">
							<input id="wpbs-settings-field-ical-file" name="ical_file_import_file" type="file" value="" />
						</div>

					</div>

					<!-- Set Legend Item -->
					<div class="wpbs-form-field">

						<label class="wpbs-label">
							<?php echo __('Import iCal Events As', 'wp-booking-system'); ?> *
						</label>

						<div class="wpbs-input">

							<select name="ical_file_import_legend_item_default">
								<option value=""><?php echo __('Select...', 'wp-booking-system'); ?></option>
								<?php
								foreach ($legend_items as $legend_item) {
									echo '<option value="' . absint($legend_item->get('id')) . '">' . $legend_item->get('name') . '</option>';
								}
								?>
							</select>

						</div>

						<div class="wpbs-label-description"><?php echo __('The dates from the iCal events present in the file will be assigned the legend item that you choose here.', 'wp-booking-system'); ?></div>

					</div>

					<!-- Overwrite existing -->
					<div class="wpbs-form-field">

						<label class="wpbs-label">
							<?php echo __('Overwrite Existing Dates', 'wp-booking-system'); ?> *
						</label>

						<div class="wpbs-input">

							<select name="ical_import_file_overwrite">
								<option value=""><?php echo __('Select...', 'wp-booking-system'); ?></option>
								<option value="overwrite"><?php echo __('Overwrite', 'wp-booking-system'); ?></option>
								<option value="skip"><?php echo __('Skip', 'wp-booking-system'); ?></option>
							</select>

						</div>

						<div class="wpbs-label-description"><?php echo __('Select whether or not to overwrite data for dates in the calendar that already contain information. Selecting "Overwrite" will add all events present in the iCal file, regardless of the existing information for the calendar dates. Selecting "Skip" will add data only for dates that do not have any information set.', 'wp-booking-system'); ?></div>

					</div>

					<!-- Description Import -->
					<div class="wpbs-form-field">

						<label class="wpbs-label">
							<?php echo __('Description Import', 'wp-booking-system'); ?> *
						</label>

						<div class="wpbs-input">

							<select name="ical_import_file_description">
								<option value=""><?php echo __('Select...', 'wp-booking-system'); ?></option>
								<option value="only_description"><?php echo __('Import only in the description field', 'wp-booking-system'); ?></option>
								<option value="only_tooltip"><?php echo __('Import only in the tooltip field', 'wp-booking-system'); ?></option>
								<option value="both"><?php echo __('Import in both description and tooltip fields', 'wp-booking-system'); ?></option>
								<option value="none"><?php echo __('Do not import the description', 'wp-booking-system'); ?></option>
							</select>

						</div>

						<div class="wpbs-label-description"><?php echo __('Select how you wish the description of each event from the .ics file to be imported into the plugin.', 'wp-booking-system'); ?></div>

					</div>

				</div>

				<div class="wpbs-box-footer-wrapper">
					<div class="wpbs-box-footer">
						<input type="submit" class="wpbs-primary-button" value="<?php echo __('Import iCal File', 'wp-booking-system'); ?>" />

						<!-- Calendar ID -->
						<input type="hidden" name="calendar_id" value="<?php echo $calendar_id; ?>" />

						<!-- Nonce -->
						<?php wp_nonce_field('wpbs_ical_import_file', 'wpbs_token', false); ?>
						<input type="hidden" name="wpbs_action" value="ical_import_file" />

					</div>
				</div>

			</form>

		</div>

		<div class="wpbs-box wpbs-box-half">
			<?php $ical_feeds = wpbs_get_calendar_meta_ical_feeds($calendar_id);  ?>

			<form id="wpbs-ical-url-import" method="POST" action="">


				<div class="wpbs-box-header wpbs-has-buttons">
					<h2>
						<?php echo __('Import from iCal URL', 'wp-booking-system'); ?>
					</h2>

					<div class="wpbs-buttons">
						<?php if (! empty($ical_feeds)): ?>
							<a onclick="return confirm('<?php echo __('Are you sure you want to refresh this iCal feeds?', 'wp-booking-system'); ?>');" href="<?php echo wp_nonce_url(add_query_arg(array('wpbs_action' => 'refresh_ical_feeds'), remove_query_arg('wpbs_message')), 'wpbs_refresh_ical_feeds', 'wpbs_token'); ?>" class="wpbs-secondary-button"><i class="dashicons dashicons-update"></i> <?php echo __('Refresh iCal Data', 'wp-booking-system'); ?></a>
						<?php endif; ?>

						<?php if (empty($ical_status) || $ical_status != true): ?>
							<a class="wpbs-ical-import-disable wpbs-secondary-button" href="<?php echo wp_nonce_url(add_query_arg(array('page' => 'wpbs-calendars', 'wpbs_action' => 'disable_icalendar_links', 'calendar_id' => $calendar_id), admin_url('admin.php')), 'wpbs_disable_icalendar_links', 'wpbs_token'); ?>"><?php echo __('Disable iCalendar Links', 'wp-booking-system'); ?></a>
						<?php else: ?>
							<a class="wpbs-ical-import-disable wpbs-primary-button" href="<?php echo wp_nonce_url(add_query_arg(array('page' => 'wpbs-calendars', 'wpbs_action' => 'enable_icalendar_links', 'calendar_id' => $calendar_id), admin_url('admin.php')), 'wpbs_enable_icalendar_links', 'wpbs_token'); ?>"><?php echo __('Enable iCalendar Links', 'wp-booking-system'); ?></a>
						<?php endif; ?>
					</div>
				</div>

				<div class="wpbs-box-content">

					<div class="wpbs-calendar-notes wpbs-ical-feeds-calendar <?php if (!empty($ical_status) && $ical_status == true): ?>disabled<?php endif; ?>">

						<?php if (empty($ical_feeds)): ?>

							<p><?php echo __("You don't have any iCal URL's included. Use the form below to add an iCal feed URL.", 'wp-booking-system'); ?></p>

						<?php else: ?>

							<?php foreach ($ical_feeds as $ical_feed): ?>
								<?php $disabled = (isset($ical_feed['disabled']) && $ical_feed['disabled'] == true) ? true : false; ?>
								<div class="wpbs-calendar-note wpbs-ical-feed-calendar">

									<div class="wpbs-calendar-note-body">
										<div class="wpbs-ical-feed-calendar-inner wpbs-ical-feed-calendar-inner-<?php echo $disabled ? 'disabled' : 'enabled'; ?>">
											<div class="text">
												<?php
												$legend_item = wpbs_get_legend_item($ical_feed['legend_item_id']);
												if ($legend_item) {
													echo wpbs_get_legend_item_icon($legend_item->get('id'), $legend_item->get('type'), $legend_item->get('color'));
												}
												?>
												<strong><?php echo $ical_feed['name']; ?></strong>
												<p title="<?php echo esc_attr($ical_feed['url']); ?>"><?php echo esc_attr($ical_feed['url']); ?></p>
											</div>

											<?php if (!$legend_item): ?>
												<span class="wpbs-ical-error"><?php echo __('The legend item assigned to this iCalendar link no longer exists.', 'wp-booking-system') ?></span>
											<?php endif; ?>

											<div class="wpbs-buttons">
												<a href="<?php echo wp_nonce_url(add_query_arg(array('wpbs_action' => 'toggle_ical_feed', 'ical_feed_id' => $ical_feed['id'])), 'wpbs_toggle_ical_feed', 'wpbs_token'); ?>" class="wpbs-secondary-button"><?php echo $disabled ? __('Enable', 'wp-simple-booking-calendar') : __('Disable', 'wp-simple-booking-calendar'); ?></a>

												<a onclick="return confirm('<?php echo __('Are you sure you want to remove this iCal URL?', 'wp-booking-system'); ?>');" href="<?php echo wp_nonce_url(add_query_arg(array('wpbs_action' => 'remove_ical_feed', 'ical_feed_id' => $ical_feed['id']), remove_query_arg('wpbs_message')), 'wpbs_remove_ical_feed', 'wpbs_token'); ?>" class="wpbs-secondary-button wpbs-remove-button"><i class="wpbs-icon-trash"></i></a>
											</div>
										</div>
									</div>

									<div class="wpbs-calendar-note-footer">
										<small>
											<?php echo sprintf(__('Last updated: %s', 'wp-booking-system'), wpbs_date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($ical_feed['last_updated']))); ?>
											| <?php _e('Split Days', 'wp-booking-system') ?>: <?php echo $ical_feed['split_days'] ? __('Yes', 'wp-booking-system') : __('No', 'wp-booking-system'); ?>
										</small>
									</div>

								</div>

							<?php endforeach; ?>


						<?php endif; ?>




						<!-- Calendar Name -->
						<div class="wpbs-form-field">

							<label class="wpbs-label"><?php echo __('Calendar Name', 'wp-booking-system'); ?> *</label>

							<div class="wpbs-input">
								<input name="ical_url_import_name" type="text" value="" />
							</div>

						</div>

						<!-- iCal URL -->
						<div class="wpbs-form-field">

							<label class="wpbs-label"><?php echo __('iCalendar URL', 'wp-booking-system'); ?> *</label>

							<div class="wpbs-input">
								<input name="ical_url_import_url" type="text" value="" />
							</div>

						</div>

						<!-- Set Legend Item -->
						<div class="wpbs-form-field">

							<label class="wpbs-label">
								<?php echo __('Import iCal Events As', 'wp-booking-system'); ?> *
							</label>

							<div class="wpbs-input">

								<select name="ical_url_import_legend_item_default">
									<option value=""><?php echo __('Select...', 'wp-booking-system'); ?></option>
									<?php
									foreach ($legend_items as $legend_item) {
										echo '<option value="' . absint($legend_item->get('id')) . '">' . $legend_item->get('name') . '</option>';
									}
									?>
								</select>

							</div>

						</div>

						<!-- Use split days -->
						<div class="wpbs-form-field">

							<label class="wpbs-label">
								<?php echo __('Use Split Days', 'wp-booking-system'); ?>

							</label>

							<div class="wpbs-input">

								<label for="ical_url_import_split_days" class="wpbs-checkbox-switch">
									<input type="checkbox" id="ical_url_import_split_days" name="ical_url_import_split_days" value="1" class="wpbs-settings-toggle">
									<div class="wpbs-checkbox-slider"></div>
								</label>

							</div>

							<div class="wpbs-label-description"><?php echo __("If selected, the start and end dates of the iCalendar Events will be displayed as Split Days. You will have to chose the legends to be used below.", 'wp-booking-system'); ?></div>

						</div>

						<!-- Set Legend Item for Starting Split Day  -->
						<div class="wpbs-form-field wpbs-form-field-import-split-days wpbs-hidden">

							<div class="wpbs-input wpbs-has-columns">

								<div class="wpbs-half">
									<label for="ical_url_import_legend_item_split_day_start"><?php echo __('Split Day Start', 'wp-booking-system'); ?></label>

									<select name="ical_url_import_legend_item_split_day_start" id="ical_url_import_legend_item_split_day_start">
										<option value=""><?php echo __('Select...', 'wp-booking-system'); ?></option>
										<?php
										foreach ($legend_items as $legend_item) {
											echo '<option value="' . absint($legend_item->get('id')) . '">' . $legend_item->get('name') . '</option>';
										}
										?>
									</select>
								</div>

								<div class="wpbs-half">
									<label for="ical_url_import_legend_item_split_day_end"><?php echo __('Split Day End', 'wp-booking-system'); ?></label>
									<select name="ical_url_import_legend_item_split_day_end" id="ical_url_import_legend_item_split_day_end">
										<option value=""><?php echo __('Select...', 'wp-booking-system'); ?></option>
										<?php
										foreach ($legend_items as $legend_item) {
											echo '<option value="' . absint($legend_item->get('id')) . '">' . $legend_item->get('name') . '</option>';
										}
										?>
									</select>
								</div>

							</div>

						</div>

					</div>

				</div>

				<div class="wpbs-box-footer-wrapper">
					<div class="wpbs-box-footer">

						<input type="submit" disabled class="wpbs-primary-button" value="<?php echo __('Add iCal URL', 'wp-booking-system'); ?>" />


						<!-- Calendar ID -->
						<input type="hidden" name="calendar_id" value="<?php echo $calendar_id; ?>" />

						<!-- Nonce -->
						<?php wp_nonce_field('wpbs_ical_import_url', 'wpbs_token', false); ?>
						<input type="hidden" name="wpbs_action" value="ical_import_url" />

					</div>
				</div>

			</form>

		</div>

	</div>

</div>