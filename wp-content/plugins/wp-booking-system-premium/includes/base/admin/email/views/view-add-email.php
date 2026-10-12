<!-- Plugin Sticky Header -->
<div class="wpbs-plugin-sticky-header wpbs-plugin-sticky-header-in-tab">

    <!-- Page Title -->
    <span class="wpbs-page-heading"><?php echo __('Add New Email Notification', 'wp-booking-system'); ?></span>

    <!-- Buttons -->
    <div class="wpbs-header-buttons">
        <a href="<?php echo add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'tab' => 'email-notifications'), admin_url('admin.php')); ?>" class="wpbs-primary-button"><?php echo __('Back to all email notifications', 'wp-booking-system'); ?></a>
    </div>

</div>

<div class="wpbs-box wpbs-box-half wpbs-box-centered wpbs-add-email-notification-box">
    <form action="" method="POST">

        <div class="wpbs-box-header">
            <h2>
                <div class="wpbs-add-icon">
                    <span class="dashicons dashicons-email-alt"></span>
                </div><?php echo __('Add New Email Notification', 'wp-booking-system'); ?>
            </h2>
        </div>

        <div class="wpbs-box-content">

            <!-- Add Email Notification Name -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="wpbs-new-email-notification-name"><?php echo __('Email Notification Name', 'wp-booking-system'); ?></label>
                <div class="wpbs-input">
                    <input id="wpbs-new-email-notification-name" name="notification_email_name" type="text" value="<?php echo (! empty($_POST['notification_email_name']) ? esc_attr($_POST['notification_email_name']) : ''); ?>" />
                </div>
            </div>

            <!-- Select Email Template -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="wpbs-new-email-template"><?php echo __('Email Template', 'wp-booking-system'); ?></label>
                <div class="wpbs-input">
                    <select id="wpbs-new-email-template" name="email_notification_template">
                        <option value=""><?php echo __('Blank Template', 'wp-booking-system'); ?></option>
                        <option value="user_notification"><?php echo __('Customer New Boooking Notification', 'wp-booking-system'); ?></option>
                        <option value="reminder_email"><?php echo __('Customer Reminder Email', 'wp-booking-system'); ?></option>
                        <option value="followup_email"><?php echo __('Customer Follow Up Email', 'wp-booking-system'); ?></option>
                        <option value="payment_email"><?php echo __('Customer Final Payment Reminder', 'wp-booking-system'); ?></option>
                        <option value="payment_confirmation"><?php echo __('Customer Final Payment Confirmation', 'wp-booking-system'); ?></option>

                    </select>
                </div>
                <div class="wpbs-label-description"><?php echo __("Select a template to start with some pre-congifured options.", 'wp-booking-system'); ?></div>
            </div>

            <div class="wpbs-box-footer-wrapper">
                <div class="wpbs-box-footer">
                    <div class="wpbs-spaced-buttons">
                        <input type="submit" class="wpbs-primary-button" value="<?php echo __('Add Email Notification', 'wp-booking-system'); ?>" />
                        <a class="wpbs-secondary-button wpbs-remove-button" href="<?php echo add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'tab' => 'email-notifications'), admin_url('admin.php')); ?>"><?php echo __('Cancel', 'wp-booking-system'); ?></a>
                    </div>

                    <!-- Action and nonce -->
                    <input type="hidden" name="email_notification_form_id" value="<?php echo absint($form_id); ?>" />
                    <input type="hidden" name="wpbs_action" value="add_email_notification" />
                    <?php wp_nonce_field('wpbs_add_email_notification', 'wpbs_token', false); ?>

                </div>
            </div>

        </div>



    </form>

</div>