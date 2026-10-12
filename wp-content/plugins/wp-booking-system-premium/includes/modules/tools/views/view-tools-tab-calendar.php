<div class="wpbs-tab-inner">

    <div class="wpbs-page-notice notice-error">

		<p><?php echo __("Be careful - these changes can't be undone. To be safe, make a backup before performing any actions.", 'wp-booking-system'); ?></p>

	</div>

    <?php $calendars = wpbs_get_calendars(['status' => 'active']); ?>

    <h2><?php _e('Copy Calendar Prices', 'wp-booking-system') ?>
        <small class="wpbs-heading-description"><?php _e('Copy the prices from one calendar to another.', 'wp-booking-system') ?></small>
    </h2>

    <div class="wpbs-form-fields-indent">

        <!-- Calendars -->
        <div class="wpbs-form-field">

            <label class="wpbs-label" for=""><?php _e('Calendars', 'wp-booking-system') ?></label>

            <div class="wpbs-input wpbs-has-columns">
                <div class="wpbs-half">
                    <label class="wpbs-label" for="copy_prices_source_calendar"><?php echo __('Source Calendar', 'wp-booking-system'); ?></label>
                    <select name="copy_prices_source_calendar" id="copy_prices_source_calendar">
                        <option value="">-</option>
                        <?php foreach ($calendars as $calendar): ?>
                            <option value="<?php echo $calendar->get('id'); ?>"><?php echo $calendar->get_name(); ?> (#<?php echo $calendar->get('id'); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="wpbs-half">
                    <label class="wpbs-label" for="copy_prices_destination_calendar"><?php echo __('Destination Calendar', 'wp-booking-system'); ?></label>
                    <select name="copy_prices_destination_calendar" id="copy_prices_destination_calendar">
                        <option value="">-</option>
                        <?php foreach ($calendars as $calendar): ?>
                            <option value="<?php echo $calendar->get('id'); ?>"><?php echo $calendar->get_name(); ?> (#<?php echo $calendar->get('id'); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

        </div>

        <a id="wpbs-copy-prices-submit-button" class="wpbs-secondary-button disabled" onclick="return confirm('<?php _e('This action cannot be undone. Are you sure you want to proceed? You will overwrite all the prices in the destination calendar.', 'wp-booking-system'); ?>');" href="<?php echo add_query_arg(array('wpbs_action' => 'copy_calendar_prices', 'wpbs_token' => wp_create_nonce('wpbs_copy_calendar_prices')), admin_url('admin.php')); ?>"><?php echo __('Copy Prices', 'wp-booking-system'); ?></a>

    </div>

    <div class="wpbs-divider"></div>

    <h2><?php _e('Copy Calendar Yearly Prices', 'wp-booking-system') ?>
        <small class="wpbs-heading-description"><?php _e('Copy the prices in a calendar from one year to another.', 'wp-booking-system') ?></small>
    </h2>

    <div class="wpbs-form-fields-indent">

        <div class="wpbs-form-field">
            <div class="wpbs-input">
                <label class="wpbs-label" for="copy_year_prices_calendar"><?php echo __('Calendar', 'wp-booking-system'); ?></label>
                <select name="copy_prices_calendar" id="copy_prices_calendar">
                    <option value="">-</option>
                    <?php foreach ($calendars as $calendar): ?>
                        <option value="<?php echo $calendar->get('id'); ?>"><?php echo $calendar->get_name(); ?> (#<?php echo $calendar->get('id'); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Years -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for=""><?php echo __('Years', 'wp-booking-system'); ?></label>
            <div class="wpbs-input wpbs-has-columns">
                <div class="wpbs-half">
                    <label class="wpbs-label" for="copy_prices_source_year"><?php echo __('Calendar', 'wp-booking-system'); ?></label>
                    <select name="copy_prices_source_year" id="copy_prices_source_year">
                        <option value="">-</option>
                        <?php for ($i = date('Y') - 3; $i <= date('Y') + 10; $i++): ?>
                            <option value="<?php echo $i ?>"><?php echo $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="wpbs-half">
                    <label class="wpbs-label" for="copy_prices_destination_year"><?php echo __('Destination Year', 'wp-booking-system'); ?></label>
                    <select name="copy_prices_destination_year" id="copy_prices_destination_year">
                        <option value="">-</option>
                        <?php for ($i = date('Y') - 3; $i <= date('Y') + 10; $i++): ?>
                            <option value="<?php echo $i ?>"><?php echo $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

        </div>

        <div class="wpbs-form-field">
            <div class="wpbs-input">
                <label class="wpbs-label" for="copy_prices_increase"><?php echo __('Price Change', 'wp-booking-system'); ?></label>
                <div class="wpbs-input-wrapper">
                    <span class="wpbs-input-symbol wpbs-input-symbol-left"><span>%</span></span>
                    <input name="copy_prices_increase" id="copy_prices_increase" type="number" step="0.001" value="">
                </div>
            </div>
            <div class="wpbs-label-description"><?php echo __('Optional. Increase the price when copying by a certain percentage. Leave empty to copy the prices wihtout modifying them. Important: If a value is provided, the default calendar price will also be copied and increased.', 'wp-booking-system'); ?></div>
        </div>

        <a id="wpbs-copy-year-prices-submit-button" class="wpbs-secondary-button disabled" onclick="return confirm('<?php _e('This action cannot be undone. Are you sure you want to proceed? You will overwrite all the prices in the calendar for the selected year.', 'wp-booking-system'); ?>');" href="<?php echo add_query_arg(array('wpbs_action' => 'copy_calendar_year_prices', 'wpbs_token' => wp_create_nonce('wpbs_copy_calendar_year_prices')), admin_url('admin.php')); ?>"><?php echo __('Copy Prices', 'wp-booking-system'); ?></a>

    </div>



</div>