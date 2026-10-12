<?php $settings = get_option('wpbs_settings'); ?>
<form>
    <div class="wpbs-tab-inner">
        <h2><?php echo __('Send an email to the customer', 'wp-booking-system'); ?></h2>


        <!-- Send To -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="booking_email_customer_send_to"><?php echo __('Send To', 'wp-booking-system'); ?></label>
            <div class="wpbs-input">
                <select name="booking_email_customer_send_to" id="booking_email_customer_send_to">
                    <?php echo $this->get_email_addresses_as_options(); ?>
                </select>
            </div>

            <a href="#" class="wpbs-link-button wpbs-form-translations-toggle"><?php echo __('Options', 'wp-booking-system'); ?> <i class="wpbs-icon-angle-down"></i></a>

            <div class="wpbs-form-translations wpbs-form-fields-indent">
                <!-- CC -->
                <div class="wpbs-form-field">
                    <label class="wpbs-label" for="booking_email_customer_send_to_cc"><?php echo __('CC', 'wp-booking-system'); ?></label>
                    <div class="wpbs-input">
                        <input name="booking_email_customer_send_to_cc" type="text" id="booking_email_customer_send_to_cc" value="<?php echo (isset($this->plugin_settings['default_cc'])) ? $this->plugin_settings['default_cc'] : ''; ?>">
                    </div>
                </div>

                <!-- BCC -->
                <div class="wpbs-form-field">
                    <label class="wpbs-label" for="booking_email_customer_send_to_bcc"><?php echo __('BCC', 'wp-booking-system'); ?></label>
                    <div class="wpbs-input">
                        <input name="booking_email_customer_send_to_bcc" type="text" id="booking_email_customer_send_to_bcc" value="<?php echo (isset($this->plugin_settings['default_bcc'])) ? $this->plugin_settings['default_bcc'] : ''; ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- From Name -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="booking_email_customer_from_name"><?php echo __('From Name', 'wp-booking-system'); ?></label>

            <div class="wpbs-input">
                <input name="booking_email_customer_from_name" type="text" id="booking_email_customer_from_name" value="<?php echo (isset($this->plugin_settings['default_from_name'])) ? $this->plugin_settings['default_from_name'] : ''; ?>">
            </div>
        </div>

        <!-- From Email -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="booking_email_customer_from_email"><?php echo __('From Email', 'wp-booking-system'); ?></label>

            <div class="wpbs-input">
                <input name="booking_email_customer_from_email" type="text" id="booking_email_customer_from_email" value="<?php echo (isset($this->plugin_settings['default_from_email'])) ? $this->plugin_settings['default_from_email'] : ''; ?>">
            </div>
        </div>

        <!-- Reply To -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="booking_email_customer_reply_to"><?php echo __('Reply To', 'wp-booking-system'); ?></label>

            <div class="wpbs-input">
                <input name="booking_email_customer_reply_to" type="text" id="booking_email_customer_reply_to" value="<?php echo (isset($this->plugin_settings['default_reply_to'])) ? $this->plugin_settings['default_reply_to'] : ''; ?>">
            </div>
        </div>

        <div class="wpbs-form-field">
            <label class="wpbs-label" for="booking_email_load_message"><?php echo __('Load Template', 'wp-booking-system'); ?></label>
            <div class="wpbs-input">
                <?php if (isset($settings['form_email_template_name']) && $settings['form_email_template_name']): ?>
                    <select name="" class="wpbs-load-tinymce-content" data-auto-load="<?php echo (isset($settings['automatically_load_template'])) ? $settings['automatically_load_template'] : '0'; ?>" id="booking_email_load_message" data-tinymce="booking_email_customer_message" data-subject="booking_email_customer_subject">
                        <option disabled selected>-</option>
                        <?php foreach ($settings['form_email_template_name'] as $i => $template_name): ?>
                            <option value="<?php echo ($i + 1); ?>" data-text="<?php echo esc_attr(wpautop($settings['form_email_template_body'][$i])); ?>"><?php echo $template_name ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
            <div class="wpbs-label-description">
                <?php echo __('You can set up message templates in', 'wp-booking-system') ?> <a target="_blank" href="<?php echo add_query_arg(array('page' => 'wpbs-settings', 'tab' => 'email'),  admin_url('admin.php')); ?>"><?php echo __('WP Booking System -> Settings -> Email', 'wp-booking-system') ?></a>
            </div>
        </div>

        <div class="wpbs-form-field">
            <label class="wpbs-label" for="booking_email_customer_subject"><?php echo __('Subject', 'wp-booking-system'); ?></label>
            <div class="wpbs-input">
                <input name="booking_email_customer_subject" type="text" id="booking_email_customer_subject" value="">
            </div>
        </div>

        <div class="wpbs-form-field">
            <label class="wpbs-label" for="booking_email_customer_message"><?php echo __('Message', 'wp-booking-system'); ?></label>
            <div class="wpbs-input">
                <div class="wpbs-wp-editor-ajax" data-id="booking_email_customer_message">
                    <?php wp_editor('', 'booking_email_customer_message', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false)); ?>
                </div>
            </div>
        </div>

        <div class="wpbs-form-field">
            <label class="wpbs-label" for="booking_email_customer_include_booking_details"><?php echo __('Include Booking Details', 'wp-booking-system'); ?></label>
            <div class="wpbs-input">
                <label for="booking_email_customer_include_booking_details" class="wpbs-checkbox-switch">
                    <input <?php echo (get_user_meta(get_current_user_id(), 'wpbs_remember_include_booking_details', true)) ? 'checked' : ''; ?> name="booking_email_customer_include_booking_details" type="checkbox" id="booking_email_customer_include_booking_details" class="wpbs-settings-toggle">
                    <div class="wpbs-checkbox-slider"></div>
                </label>
            </div>
        </div>

        <?php do_action('wpbs_booking_modal_email_customer_after', $this->booking) ?>



    </div>


    <!-- Save Page Button -->
    <div class="wpbs-box-footer-wrapper">
        <div class="wpbs-box-footer">
            <button class="wpbs-primary-button" id="wpbs-email-customer" data-booking-id="<?php echo $this->booking->get('id'); ?>">
                <?php echo __('Send Email', 'wp-booking-system'); ?>
            </button>
        </div>
    </div>
</form>