<?php
$defaults = wpbs_settings_bank_transfer_defaults();
?>

<div class="wpbs-tab-inner">

    <h2>
        <?php echo __('Bank Transfer', 'wp-booking-system'); ?>
        <small class="wpbs-heading-description"><?php echo __("Give the customer the option to transfer money directly in your bank account.", 'wp-booking-system'); ?></small>
    </h2>


    <!-- Enable Bank Transfer -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="payment_bt_enable">
            <?php echo __('Enable Bank Transfer', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <label for="payment_bt_enable" class="wpbs-checkbox-switch">
                <input type="hidden" name="wpbs_settings[payment_bt_enable]" value="0">
                <input data-target="#wpbs-bank-transfer" name="wpbs_settings[payment_bt_enable]" type="checkbox" id="payment_bt_enable" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($settings['payment_bt_enable'])) ? 'checked' : ''; ?>>
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>
    </div>

    <div id="wpbs-bank-transfer" class="wpbs-bank-transfer-wrapper wpbs-settings-wrapper <?php echo (!empty($settings['payment_bt_enable'])) ? 'wpbs-settings-wrapper-show' : ''; ?>">

        <!-- Payment Method Name -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="payment_bt_name">
                <?php echo __('Display name', 'wp-booking-system'); ?>

            </label>

            <div class="wpbs-input">
                <input name="wpbs_settings[payment_bt_name]" type="text" id="payment_bt_name" class="" value="<?php echo (!empty($settings['payment_bt_name'])) ? $settings['payment_bt_name'] : $defaults['display_name']; ?>">
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
                                    <input name="wpbs_settings[payment_bt_name_translation_<?php echo $language; ?>]" type="text" id="payment_bt_name_translation_<?php echo $language; ?>" value="<?php echo (!empty($settings['payment_bt_name_translation_' . $language])) ? esc_attr($settings['payment_bt_name_translation_' . $language]) : ''; ?>" >
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>


        <!-- Payment Method Description -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="payment_bt_description">
                <?php echo __('Description', 'wp-booking-system'); ?>

            </label>

            <div class="wpbs-input">
                <input name="wpbs_settings[payment_bt_description]" type="text" id="payment_bt_description" class="" value="<?php echo (!empty($settings['payment_bt_description'])) ? $settings['payment_bt_description'] : $defaults['description']; ?>">
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
                                    <input name="wpbs_settings[payment_bt_description_translation_<?php echo $language; ?>]" type="text" id="payment_bt_description_translation_<?php echo $language; ?>" value="<?php echo (!empty($settings['payment_bt_description_translation_' . $language])) ? esc_attr($settings['payment_bt_description_translation_' . $language]) : ''; ?>" >
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>


        <!-- Payment Instructions -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="payment_bt_instructions">
                <?php echo __('Payment Instructions', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <?php wp_editor((!empty($settings['payment_bt_instructions']) ? html_entity_decode($settings['payment_bt_instructions']) : ''), 'payment_bt_instructions', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false, 'textarea_name' => 'wpbs_settings[payment_bt_instructions]')) ?>
            </div>


            <div class="wpbs-label-description"><?php echo __('You can use the <span class="wpbs-small-print-tag wpbs-select-on-click">{Amount}</span> tag to show the total amount needed to be paid by the customer, or the <span class="wpbs-small-print-tag wpbs-select-on-click">{Booking ID}</span> tag to add a transaction reference number.', 'wp-booking-system') ?></div>

            <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-link-button wpbs-form-translations-toggle"><?php echo __('Translations', 'wp-booking-system'); ?> <i class="wpbs-icon-angle-down"></i></a><?php endif ?>


            <?php if (wpbs_translations_active()): ?>
                <!-- Required Field Translations -->
                <div class="wpbs-form-translations wpbs-form-fields-indent">
                    <?php foreach ($active_languages as $language): ?>
                        <div class="wpbs-form-field wpbs-absolute-label">
                            <label class="wpbs-label"><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo $languages[$language]; ?></label>
                            <div class="wpbs-input">
                                <?php wp_editor((!empty($settings['payment_bt_instructions_translation_' . $language]) ? html_entity_decode($settings['payment_bt_instructions_translation_' . $language]) : ''), 'payment_bt_instructions_translation_' . $language, array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false, 'textarea_name' => 'wpbs_settings[payment_bt_instructions_translation_' . $language . ']')) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>


    </div>
</div>