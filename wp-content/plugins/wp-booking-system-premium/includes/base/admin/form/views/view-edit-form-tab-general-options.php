<div class="wpbs-dynamic-tags-wrapper">

    <!-- Email Tags -->
    <div class="wpbs-dynamic-tags">
        <h2 class="title"><?php echo __('Dynamic Tags', 'wp-booking-system'); ?></h2>
        <p><?php echo __('You can use these dynamic tags in the Event Description and Event Tooltip fields. They will be replaced with the values submitted in the form.', 'wp-booking-system'); ?></p>

        <?php wpbs_output_email_tags($form_data); ?>
    </div>


    <!-- Dynamic Tags Content -->
    <div class="wpbs-tab-inner">

        <h2><?php echo __('General Options', 'wp-booking-system'); ?></h2>

        <!-- Submit Button -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="submit_button_label"><?php echo __('Submit Button Label', 'wp-booking-system'); ?></label>

            <div class="wpbs-input">
                <input name="submit_button_label" type="text" id="submit_button_label" value="<?php echo (!empty($form_meta['submit_button_label'][0])) ? esc_attr($form_meta['submit_button_label'][0]) : ''; ?>" >
            </div>

            <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-link-button wpbs-form-translations-toggle"><?php echo __('Translations', 'wp-booking-system'); ?> <i class="wpbs-icon-angle-down"></i></a><?php endif ?>

            <?php if (wpbs_translations_active()): ?>
                <!-- Required Field Translations -->
                <div class="wpbs-form-translations wpbs-form-fields-indent">
                    <?php foreach ($active_languages as $language): ?>
                        <div class="wpbs-form-field wpbs-small-margin">
                            <div class="wpbs-input">
                                <div class="wpbs-input-wrapper">
                                    <span class="wpbs-input-symbol wpbs-input-symbol-left wpbs-flag-symbol">
                                        <span><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo strtoupper($language); ?></span>
                                    </span>
                                    <input name="submit_button_label_translation_<?php echo $language; ?>" type="text" id="submit_button_label_translation_<?php echo $language; ?>" value="<?php echo (!empty($form_meta['submit_button_label_translation_' . $language][0])) ? esc_attr($form_meta['submit_button_label_translation_' . $language][0]) : ''; ?>" >
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif ?>
        </div>


        <!-- Tracking Script -->
        <div class="wpbs-form-field">

            <label class="wpbs-label" for="tracking_script">
                <?php echo __('Tracking Script', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <textarea name="tracking_script" id="tracking_script" rows="3" ><?php echo (!empty($form_meta['tracking_script'][0])) ? esc_textarea($form_meta['tracking_script'][0]) : ''; ?></textarea>
            </div>

            <div class="wpbs-label-description"><?php echo __("Tracking script code (eg. Google Analytics) that's executed after the form was successfully submitted.", 'wp-booking-system'); ?> <?php echo __('Do not include the &lt;script&gt; tags.', 'wp-booking-system'); ?></div>


        </div>

        <!-- Auto-Complete Event Description -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="autofill_event_description">
                <?php echo __('Autofill Event Description', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <input name="autofill_event_description" type="text" id="autofill_event_description" value="<?php echo (!empty($form_meta['autofill_event_description'][0])) ? esc_attr($form_meta['autofill_event_description'][0]) : ''; ?>" >
            </div>

            <div class="wpbs-label-description"><?php echo __("Automatically fill in the event description field when a booking is made. You can use Email Tags to populate with dynamic form values, eg. '{1:Your Name}'. This only works if the Automatically Block Dates option is enabled.", 'wp-booking-system'); ?></div>
        </div>

        <!-- Auto-Complete Event Tooltip -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="autofill_event_tooltip">
                <?php echo __('Autofill Event Tooltip', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <input name="autofill_event_tooltip" type="text" id="autofill_event_tooltip" value="<?php echo (!empty($form_meta['autofill_event_tooltip'][0])) ? esc_attr($form_meta['autofill_event_tooltip'][0]) : ''; ?>" >
            </div>

            <div class="wpbs-label-description"><?php echo __("Automatically fill in the event tooltip field when a booking is made. You can use Email Tags to populate with dynamic form values, eg. '{1:Your Name}'. This only works if the Automatically Block Dates option is enabled.", 'wp-booking-system'); ?></div>
        </div>

        <!-- Confirmation Type -->
        <?php $default_status = (!empty($form_meta['form_default_booking_status'][0])) ? esc_attr($form_meta['form_default_booking_status'][0]) : 'pending'; ?>
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="form_default_booking_status">
                <?php echo __('Default Booking Status', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <select name="form_default_booking_status" id="form_default_booking_status">
                    <option <?php echo ($default_status == 'pending') ? 'selected' : ''; ?> value="pending"><?php echo __('Pending', 'wp-booking-system'); ?></option>
                    <option <?php echo ($default_status == 'accepted') ? 'selected' : ''; ?> value="accepted"><?php echo __('Accepted', 'wp-booking-system'); ?></option>
                </select>
            </div>
        </div>

        <?php

        /**
         * Hook to add extra fields at the bottom of the General Tab
         *
         * @param array $settings
         *
         */
        do_action('wpbs_submenu_page_edit_form_tab_general_bottom', $settings);

        ?>

    </div>

</div>