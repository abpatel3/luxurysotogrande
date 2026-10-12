<?php $settings = get_option('wpbs_settings') ?>

<div class="wpbs-tab-inner">

    <h2>
        <?php echo __('Security Deposit', 'wp-booking-system'); ?>
        <small class="wpbs-heading-description"><?php echo __('Charge a security deposit that will be automatically refunded to the customer after X days. If enabled, a Security Deposit field will become available in the Form Builder.', 'wp-booking-system'); ?></small>
    </h2>

    <!-- Enable Security Deposits -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="payment_security_deposit_enable">
            <?php echo __('Enable Security Deposits', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <label for="payment_security_deposit_enable" class="wpbs-checkbox-switch">
                <input type="hidden" name="wpbs_settings[payment_security_deposit_enable]" value="0">
                <input data-target="#wpbs-security-deposit-wrapper" name="wpbs_settings[payment_security_deposit_enable]" type="checkbox" id="payment_security_deposit_enable" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($settings['payment_security_deposit_enable'])) ? 'checked' : ''; ?>>
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>
    </div>

    <div id="wpbs-security-deposit-wrapper" class="wpbs-form-fields-indent wpbs-payment-security-deposit-wrapper wpbs-settings-wrapper <?php echo (!empty($settings['payment_security_deposit_enable'])) ? 'wpbs-settings-wrapper-show' : ''; ?>">

        <!-- Taxable -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="payment_security_deposit_taxable">
                <?php echo __('Taxable', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <select name="wpbs_settings[payment_security_deposit_taxable]" id="payment_security_deposit_taxable">
                    <option <?php selected((isset($settings['payment_security_deposit_taxable']) ? $settings['payment_security_deposit_taxable'] : ''), 'no') ?> value="no"><?php echo __('No, do not apply taxes or VAT to the security deposit', 'wp-booking-system') ?></option>
                    <option <?php selected((isset($settings['payment_security_deposit_taxable']) ? $settings['payment_security_deposit_taxable'] : ''), 'yes') ?> value="yes"><?php echo __('Yes, apply taxes or VAT to the security deposit', 'wp-booking-system') ?></option>
                </select>
            </div>
        </div>

        <!-- When to refund -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="payment_security_deposit_when_to_refund"><?php echo __('When to refund', 'wp-booking-system'); ?></label>

            <div class="wpbs-input">
                <div class="wpbs-input-wrapper">
                    <input name="wpbs_settings[payment_security_deposit_when_to_refund]" type="number" id="payment_security_deposit_when_to_refund" value="<?php echo (!empty($settings['payment_security_deposit_when_to_refund']) ? esc_attr($settings['payment_security_deposit_when_to_refund']) : '7'); ?>">
                    <span class="wpbs-input-symbol wpbs-input-symbol-right"><span><?php echo __('days after the booking ends', 'wp-booking-system'); ?></span></span>
                </div>
            </div>

        </div>

    </div>

</div>