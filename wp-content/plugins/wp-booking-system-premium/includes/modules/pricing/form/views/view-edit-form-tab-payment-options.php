<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$form_id   = absint(! empty($_GET['form_id']) ? $_GET['form_id'] : 0);
$form      = wpbs_get_form($form_id);

$form_data = $form->get('fields');
$form_meta = wpbs_get_form_meta($form_id);

$settings = get_option('wpbs_settings', array());
$active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());
$languages = wpbs_get_languages();
?>

<div class="wpbs-tab-inner">

    <h2><?php echo __('Payment Options', 'wp-booking-system'); ?></h2>

    <!-- Product Name -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="product_name">
            <?php echo __('Product Name', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <input name="product_name" id="product_name" placeholder="<?php echo isset($settings['payment_product_name']) ? $settings['payment_product_name'] : ''; ?>" type="text" value="<?php echo (!empty($form_meta['product_name'][0]) ? esc_attr($form_meta['product_name'][0]) : ''); ?>" />
        </div>

        <div class="wpbs-label-description"><?php echo __('The product name to show in the price calculator for date selections. For eg. "Days". If the users selects 4 days in the calendar, the price calculation will show "Days &times; 4: 100 EUR". ', 'wp-booking-system') . '<strong>' . __('This overwrites the default Product Name specified in the plugin\'s Settings page.', 'wp-booking-system') . '</strong>'; ?></div>

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
                                <input name="product_name_translation_<?php echo $language; ?>" placeholder="<?php echo isset($settings['payment_product_name_translation_' . $language]) ? $settings['payment_product_name_translation_' . $language] : ''; ?>" type="text" id="product_name_translation_<?php echo $language; ?>" value="<?php echo (!empty($form_meta['product_name_translation_' . $language][0])) ? esc_attr($form_meta['product_name_translation_' . $language][0]) : ''; ?>">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>


    </div>

    <!-- Multiplication Field -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="multiplication_field">
            <?php echo __('Multiplication Field', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <select name="multiplication_field" type="text" id="multiplication_field">
                <option value="">-</option>
                <?php foreach ($form_data as $field): if (!in_array($field['type'], array('radio', 'dropdown', 'checkbox', 'product_radio', 'product_dropdown', 'product_number', 'product_checkbox', 'inventory', 'number'))) continue; ?>
                    <option <?php echo (!empty($form_meta['multiplication_field'][0]) && $form_meta['multiplication_field'][0] == $field['id']) ? 'selected' : ''; ?> value="<?php echo $field['id']; ?>"><?php echo (isset($field['values']['default']['label']) && !empty($field['values']['default']['label']) ? $field['values']['default']['label'] : '(no label - ' . str_replace('_', ' ', $field['type']) . ' field)') ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="wpbs-label-description"><?php echo __("Multiply the calendar events price by the value of a form field (eg. number of people, inventory..).", 'wp-booking-system'); ?></div>

    </div>

    <?php
    $vat = new WPBS_VAT;
    if ($vat->is_enabled()): ?>
        <!-- Custom VAT -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="custom_vat_percentage">
                <?php echo __('Custom VAT Percentage', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <input name="custom_vat_percentage" id="custom_vat_percentage" placeholder="<?php echo isset($settings['payment_vat_percentage']) ? $settings['payment_vat_percentage'] : ''; ?>" type="number" min="0" step="0.01" value="<?php echo (!empty($form_meta['custom_vat_percentage'][0]) ? esc_attr($form_meta['custom_vat_percentage'][0]) : ''); ?>" />
            </div>

            <div class="wpbs-label-description"><?php echo __('Set a custom VAT percentage to be used for this form. This will overwrite the default value set in Settings -> Payment Options -> Taxes.', 'wp-booking-system') ?></div>

        </div>
    <?php endif; ?>

    <!-- Hide 0 line items -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="hide_zero_line_items">
            <?php echo __('Hide Free Line Items', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <label for="hide_zero_line_items" class="wpbs-checkbox-switch">
                <input type="hidden" name="hide_zero_line_items" value="0">
                <input name="hide_zero_line_items" type="checkbox" id="hide_zero_line_items" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($form_meta['hide_zero_line_items'][0])) ? 'checked' : ''; ?>>
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>

        <div class="wpbs-label-description"><?php echo __("Hide line items that are equal to 0 from the pricing table.", 'wp-booking-system'); ?></div>

    </div>

    <!-- Show date in pricing table -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="show_date_in_pricing_table">
            <?php echo __('Show Date Selection in Pricing Table', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <label for="show_date_in_pricing_table" class="wpbs-checkbox-switch">
                <input type="hidden" name="show_date_in_pricing_table" value="0">
                <input name="show_date_in_pricing_table" type="checkbox" id="show_date_in_pricing_table" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($form_meta['show_date_in_pricing_table'][0])) ? 'checked' : ''; ?>>
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>

        <div class="wpbs-label-description"><?php echo __("Show the selected dates in the pricing table, below the Product Name", 'wp-booking-system'); ?></div>

    </div>

    <!-- Show date in pricing table -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="show_price_breakdown_in_pricing_table">
            <?php echo __('Show Price Breakdown in Pricing Table', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <label for="show_price_breakdown_in_pricing_table" class="wpbs-checkbox-switch">
                <input type="hidden" name="show_price_breakdown_in_pricing_table" value="0">
                <input name="show_price_breakdown_in_pricing_table" type="checkbox" id="show_price_breakdown_in_pricing_table" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($form_meta['show_price_breakdown_in_pricing_table'][0])) ? 'checked' : ''; ?>>
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>

        <div class="wpbs-label-description"><?php echo __("Add a toggle to show/hide the price for each individual day in the pricing table.", 'wp-booking-system'); ?></div>

    </div>

    <?php if (isset($settings['payment_bt_enable']) && $settings['payment_bt_enable']): ?>
        <!-- Payment Instructions -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="bt_instructions">
                <?php echo __('Bank Transfer Payment Instructions', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <?php wp_editor((!empty($form_meta['bt_instructions'][0]) ? html_entity_decode($form_meta['bt_instructions'][0]) : ''), 'bt_instructions', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false, 'textarea_name' => 'bt_instructions')) ?>
            </div>

            <div class="wpbs-label-description">
                <?php echo __('You can use the <span class="wpbs-small-print-tag wpbs-select-on-click">{Amount}</span> tag to show the total amount needed to be paid by the customer, or the <span class="wpbs-small-print-tag wpbs-select-on-click">{Booking ID}</span> tag to add a transaction reference number.', 'wp-booking-system') ?>

                <?php echo __('Instructions on how and where to send the money. Bank name, account number, etc. This information will be included in the form confirmation message and in the email if the <span class="wpbs-small-print-tag wpbs-select-on-click">{Bank Transfer Instructions}</span> tag is used.', 'wp-booking-system') . ' <strong>' . __('This overwrites the default Payment Instructions added in the plugin\'s Settings page.', 'wp-booking-system') . '</strong>'; ?>
            </div>

            <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-link-button wpbs-form-translations-toggle"><?php echo __('Translations', 'wp-booking-system'); ?> <i class="wpbs-icon-angle-down"></i></a><?php endif ?>

            <?php if (wpbs_translations_active()): ?>
                <!-- Required Field Translations -->
                <div class="wpbs-form-translations wpbs-form-fields-indent">
                    <?php foreach ($active_languages as $language): ?>
                        <div class="wpbs-form-field wpbs-absolute-label">
                            <label class="wpbs-label"><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo $languages[$language]; ?></label>
                            <div class="wpbs-input">
                                <?php wp_editor((!empty($form_meta['bt_instructions_translation_' . $language][0]) ? html_entity_decode($form_meta['bt_instructions_translation_' . $language][0]) : ''), 'bt_instructions_translation_' . $language, array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false, 'textarea_name' => 'bt_instructions_translation_' . $language)) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    <?php endif; ?>

    <?php

    /**
     * Hook to add extra fields at the bottom of the General Tab
     *
     * @param array $settings
     *
     */
    do_action('wpbs_submenu_page_edit_form_tab_payment_options_bottom', $settings);

    ?>
</div>