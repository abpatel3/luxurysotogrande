<?php
$default_strings = wpbs_form_default_strings();
$strings = wpbs_form_default_string_values();
?>

<div class="wpbs-tab-inner">

    <h2><?php echo __('Form Strings', 'wp-booking-system'); ?></h2>

    <!-- Enable Notification -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="overwrite_strings_and_translations">
            <?php echo __('Use Custom Strings', 'wp-booking-system'); ?>

        </label>

        <div class="wpbs-input">
            <label for="overwrite_strings_and_translations" class="wpbs-checkbox-switch">
                <input type="hidden" name="overwrite_strings_and_translations" value="0">
                <input data-target="#wpbs-overwrite-strings-and-translations" name="overwrite_strings_and_translations" type="checkbox" id="overwrite_strings_and_translations" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($form_meta['overwrite_strings_and_translations'][0])) ? 'checked' : ''; ?>>
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>

        <div class="wpbs-label-description"><?php echo __("Use custom Strings and Translations for this form instead of the default Strings and Translations from the Settings page.", 'wp-booking-system'); ?></div>
    </div>

    <div id="wpbs-overwrite-strings-and-translations" class="wpbs-user-notification-wrapper wpbs-settings-wrapper <?php echo (!empty($form_meta['overwrite_strings_and_translations'][0])) ? 'wpbs-settings-wrapper-show' : ''; ?>">

        <?php $i = 0;
        foreach ($strings as $section): ?>

            <?php if ($i != 0): ?>
                <div class="wpbs-divider"></div>
            <?php endif; ?>
            <h2><?php echo $section['label'] ?></h2>

            <?php foreach ($section['strings'] as $key => $string): ?>
                <!-- Required Field -->
                <div class="wpbs-form-field">
                    <label class="wpbs-label" for="form_strings_<?php echo $key; ?>">
                        <?php echo $string['label'] ?>

                    </label>
                    <div class="wpbs-input">
                        <input name="form_strings_<?php echo $key; ?>" type="text" id="form_strings_<?php echo $key; ?>" value="<?php echo (!empty($form_meta['form_strings_' . $key][0])) ? esc_attr($form_meta['form_strings_' . $key][0]) : $default_strings[$key]; ?>" >
                    </div>

                    <?php if (isset($string['tooltip'])): ?>
                        <div class="wpbs-label-description"><?php echo $string['tooltip']; ?></div>
                    <?php endif ?>

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
                                            <input name="form_strings_<?php echo $key; ?>_translation_<?php echo $language; ?>" type="text" id="form_strings_<?php echo $key; ?>_translation_<?php echo $language; ?>" value="<?php echo (!empty($form_meta['form_strings_' . $key . '_translation_' . $language][0])) ? esc_attr($form_meta['form_strings_' . $key . '_translation_' . $language][0]) : ''; ?>" >
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif ?>
                </div>
            <?php endforeach; ?>
        <?php $i++;
        endforeach; ?>

    </div>

</div>