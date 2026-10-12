<?php
$defaults = wpbs_settings_payment_on_arrival_defaults();
?>

<div class="wpbs-tab-inner">

<h2>
    <?php echo __('Payment on Arrival', 'wp-booking-system'); ?>
    <small class="wpbs-heading-description"><?php echo __("Give the customer the option to pay with cash on arrival.", 'wp-booking-system'); ?></small>
</h2>

<!-- Enable Payment on Arrival -->
<div class="wpbs-form-field">
    <label class="wpbs-label" for="payment_poa_enable">
        <?php echo __('Enable Payment on Arrival', 'wp-booking-system'); ?>
    </label>

    <div class="wpbs-input">
        <label for="payment_poa_enable" class="wpbs-checkbox-switch">
            <input type="hidden" name="wpbs_settings[payment_poa_enable]" value="0">
            <input data-target="#wpbs-payment-on-arrival" name="wpbs_settings[payment_poa_enable]" type="checkbox" id="payment_poa_enable" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($settings['payment_poa_enable'])) ? 'checked' : ''; ?>>
            <div class="wpbs-checkbox-slider"></div>
        </label>
    </div>
</div>

<div id="wpbs-payment-on-arrival" class="wpbs-payment-on-arrival-wrapper wpbs-settings-wrapper <?php echo (!empty($settings['payment_poa_enable'])) ? 'wpbs-settings-wrapper-show' : ''; ?>">

    <!-- Payment Method Name -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="payment_poa_name">
            <?php echo __('Display name', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <input name="wpbs_settings[payment_poa_name]" type="text" id="payment_poa_name" class="" value="<?php echo (!empty($settings['payment_poa_name'])) ? $settings['payment_poa_name'] : $defaults['display_name']; ?>">
        </div>

        <div class="wpbs-label-description"><?php echo __("The payment method name that appears on the booking form.", 'wp-booking-system'); ?></div>

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
                                <input name="wpbs_settings[payment_poa_name_translation_<?php echo $language; ?>]" type="text" id="payment_poa_name_translation_<?php echo $language; ?>" value="<?php echo (!empty($settings['payment_poa_name_translation_' . $language])) ? esc_attr($settings['payment_poa_name_translation_' . $language]) : ''; ?>" >
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>


    <!-- Payment Method Description -->
    <div class="wpbs-settings-field-translation-wrapper">
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="payment_poa_description">
                <?php echo __('Description', 'wp-booking-system'); ?>

            </label>

            <div class="wpbs-input">
                <input name="wpbs_settings[payment_poa_description]" type="text" id="payment_poa_description" class="" value="<?php echo (!empty($settings['payment_poa_description'])) ? $settings['payment_poa_description'] : $defaults['description']; ?>">
            </div>

            <div class="wpbs-label-description"><?php echo __("The payment method description that appears on the booking form.", 'wp-booking-system'); ?></div>

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
                                    <input name="wpbs_settings[payment_poa_description_translation_<?php echo $language; ?>]" type="text" id="payment_poa_description_translation_<?php echo $language; ?>" value="<?php echo (!empty($settings['payment_poa_description_translation_' . $language])) ? esc_attr($settings['payment_poa_description_translation_' . $language]) : ''; ?>" >
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

</div>