<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$default_strings = wpbs_form_default_strings();
$strings = wpbs_form_default_string_values();
?>

<div class="wpbs-tab-inner">

    <?php

    $i = 0;
    foreach ($strings as $section): ?>

        <?php if ($i != 0): ?>
            <div class="wpbs-divider"></div>
        <?php endif; ?>
        <h2><?php echo $section['label'] ?></h2>

        <?php if ($i == 0): ?>
            <div class="wpbs-page-notice notice-info wpbs-form-changed-notice">
                <p><?php echo __("These are the default form strings and translations. They can be overwritten individually for each form on its settings page.", 'wp-booking-system') ?></p>
            </div>
        <?php endif; ?>

        <?php foreach ($section['strings'] as $key => $string): ?>
            <!-- Required Field -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="form_strings_<?php echo $key; ?>">
                    <?php echo $string['label'] ?>
                </label>

                <div class="wpbs-input">
                    <input name="wpbs_settings[form_strings_<?php echo $key; ?>]" type="text" id="form_strings_<?php echo $key; ?>" value="<?php echo (!empty($settings['form_strings_' . $key])) ? esc_attr($settings['form_strings_' . $key]) : $default_strings[$key]; ?>">
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
                                        <input name="wpbs_settings[form_strings_<?php echo $key; ?>_translation_<?php echo $language; ?>]" type="text" id="form_strings_<?php echo $key; ?>_translation_<?php echo $language; ?>" value="<?php echo (!empty($settings['form_strings_' . $key . '_translation_' . $language])) ? esc_attr($settings['form_strings_' . $key . '_translation_' . $language]) : ''; ?>">
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