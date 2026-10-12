<div class="wpbs-tab-inner">

    <?php
    $calendars = wpbs_get_calendars(array('status' => 'active'));
    $forms = wpbs_get_forms(array('status' => 'active'));

    $fields_dropdown = [];
    foreach ($forms as $form) {
        $fields = $form->get('fields');
        $available_fields = [];
        foreach ($fields as $field) {
            if (!in_array($field['type'], array('radio', 'dropdown', 'checkbox', 'product_radio', 'product_dropdown', 'product_number', 'product_checkbox', 'inventory', 'number'))) continue;
            $available_fields[$form->get('id') . '_' . $field['id']] = (isset($field['values']['default']['label']) && $field['values']['default']['label'] ? $field['values']['default']['label'] : '(no label - ' . str_replace('_', ' ', $field['type']) . ' field)');
        }

        if (!empty($available_fields)) {
            $fields_dropdown[$form->get('id')] = array(
                'form_name' => $form->get('name'),
                'fields' => $available_fields
            );
        }
    }
    ?>

    <h2><?php echo __('Taxes', 'wp-booking-system'); ?></h2>

    <!-- Enable Taxes -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="payment_tax_enable">
            <?php echo __('Enable Taxes', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <label for="payment_tax_enable" class="wpbs-checkbox-switch">
                <input type="hidden" name="wpbs_settings[payment_tax_enable]" value="0">
                <input data-target="#wpbs-tax-wrapper" name="wpbs_settings[payment_tax_enable]" type="checkbox" id="payment_tax_enable" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($settings['payment_tax_enable'])) ? 'checked' : ''; ?>>
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>
    </div>

    <div id="wpbs-tax-wrapper" class="wpbs-payment-tax-wrapper wpbs-settings-wrapper <?php echo (!empty($settings['payment_tax_enable'])) ? 'wpbs-settings-wrapper-show' : ''; ?>">

        <div class="wpbs-tax-wrapper">

            <div class="wpbs-tax-fields ">
                <?php if (!isset($settings['payment_tax_name'])) {
                    $settings['payment_tax_name'] = array('');
                }
                ?>
                <?php for ($i = 0; $i < count($settings['payment_tax_name']); $i++): ?>
                    <div class="wpbs-tax-field">
                        <div class="wpbs-form-fields-indent wpbs-form-fields-repeater">

                            <!-- Tax Name -->
                            <div class="wpbs-form-field">
                                <label class="wpbs-label" for="payment_tax_name"><?php echo __('Name', 'wp-booking-system'); ?></label>
                                <div class="wpbs-input">
                                    <input name="wpbs_settings[payment_tax_name][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_name][id]" type="text" value="<?php echo (!empty($settings['payment_tax_name'][$i]) ? esc_attr($settings['payment_tax_name'][$i]) : ''); ?>" />
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
                                                        <input name="wpbs_settings[payment_tax_name_translation_<?php echo $language; ?>][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_name_translation_<?php echo $language; ?>][id]" type="text" id="payment_tax_name_translation_<?php echo $language; ?>_<?php echo $i; ?>" value="<?php echo (!empty($settings['payment_tax_name_translation_' . $language][$i])) ? esc_attr($settings['payment_tax_name_translation_' . $language][$i]) : ''; ?>" >
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Tax Type -->
                            <div class="wpbs-form-field">
                                <label class="wpbs-label" for="payment_tax_type"><?php echo __('Type', 'wp-booking-system'); ?></label>
                                <div class="wpbs-input">
                                    <select name="wpbs_settings[payment_tax_type][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_type][id]" class="wpbs-payment_tax_type">
                                        <option <?php isset($settings['payment_tax_type'][$i]) ? selected('percentage', $settings['payment_tax_type'][$i]) : '' ?> value="percentage"><?php echo __('Percentage', 'wp-booking-system'); ?></option>
                                        <option <?php isset($settings['payment_tax_type'][$i]) ? selected('fixed_amount', $settings['payment_tax_type'][$i]) : '' ?> value="fixed_amount"><?php echo __('Fixed Amount', 'wp-booking-system'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tax Type - Percentage -->
                            <div class="wpbs-form-field wpbs-tax-type wpbs-tax-type-percentage <?php echo (isset($settings['payment_tax_type'][$i]) && $settings['payment_tax_type'][$i] != 'percentage') ? 'wpbs-hide' : ''; ?>">
                                <label class="wpbs-label" for="payment_tax_percentage"><?php echo __('Amount', 'wp-booking-system'); ?></label>
                                <div class="wpbs-input">
                                    <div class="wpbs-input-wrapper">
                                        <span class="wpbs-input-symbol wpbs-input-symbol-left"><span>%</span></span>
                                        <input name="wpbs_settings[payment_tax_percentage][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_percentage][id]" type="number" min="0" step="0.001" value="<?php echo (!empty($settings['payment_tax_percentage'][$i]) ? esc_attr($settings['payment_tax_percentage'][$i]) : ''); ?>" />
                                    </div>
                                </div>
                            </div>

                            <!-- Tax Type - Fixed Amount -->
                            <div class="wpbs-form-field wpbs-tax-type wpbs-tax-type-fixed_amount <?php echo (!isset($settings['payment_tax_type'][$i]) || $settings['payment_tax_type'][$i] != 'fixed_amount') ? 'wpbs-hide' : ''; ?>">
                                <label class="wpbs-label" for="payment_tax_fixed_amount"><?php echo __('Amount', 'wp-booking-system'); ?></label>
                                <div class="wpbs-input">
                                    <div class="wpbs-input-wrapper">
                                        <span class="wpbs-input-symbol wpbs-input-symbol-left"><span><?php echo wpbs_get_currency() ?></span></span>
                                        <input name="wpbs_settings[payment_tax_fixed_amount][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_fixed_amount][id]" type="number" min="0" step="0.01" value="<?php echo (!empty($settings['payment_tax_fixed_amount'][$i]) ? esc_attr($settings['payment_tax_fixed_amount'][$i]) : ''); ?>" />
                                    </div>
                                </div>
                            </div>

                            <!-- Tax Calculation -->
                            <div class="wpbs-form-field wpbs-tax-type wpbs-tax-type-fixed_amount <?php echo (!isset($settings['payment_tax_type'][$i]) || $settings['payment_tax_type'][$i] != 'fixed_amount') ? 'wpbs-hide' : ''; ?>">
                                <label class="wpbs-label" for="payment_tax_calculation"><?php echo __('Calculation', 'wp-booking-system'); ?></label>
                                <div class="wpbs-input">
                                    <select name="wpbs_settings[payment_tax_calculation][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_calculation][id]">
                                        <option <?php isset($settings['payment_tax_calculation'][$i]) ? selected('per_booking', $settings['payment_tax_calculation'][$i]) : '' ?> value="per_booking"><?php echo __('Per Booking - Only add once per booking', 'wp-booking-system'); ?></option>
                                        <option <?php isset($settings['payment_tax_calculation'][$i]) ? selected('per_day', $settings['payment_tax_calculation'][$i]) : '' ?> value="per_day"><?php echo __('Per Day - Multiply by the number of booked days', 'wp-booking-system'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tax Application -->
                            <div class="wpbs-form-field wpbs-tax-type wpbs-tax-type-percentage <?php echo (!isset($settings['payment_tax_type'][$i]) || $settings['payment_tax_type'][$i] != 'percentage') ? 'wpbs-hide' : ''; ?>">
                                <label class="wpbs-label" for="payment_tax_application">
                                    <?php echo __('Apply tax to', 'wp-booking-system'); ?>
                                </label>
                                <div class="wpbs-input">
                                    <select name="wpbs_settings[payment_tax_application][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_application][id]">
                                        <option <?php isset($settings['payment_tax_application'][$i]) ? selected('all', $settings['payment_tax_application'][$i]) : '' ?> value="all"><?php echo __('Calendar and Form Prices', 'wp-booking-system'); ?></option>
                                        <option <?php isset($settings['payment_tax_application'][$i]) ? selected('calendar', $settings['payment_tax_application'][$i]) : '' ?> value="calendar"><?php echo __('Calendar Price Only', 'wp-booking-system'); ?></option>
                                        <option <?php isset($settings['payment_tax_application'][$i]) ? selected('form', $settings['payment_tax_application'][$i]) : '' ?> value="form"><?php echo __('Form Price Only', 'wp-booking-system'); ?></option>
                                    </select>
                                </div>

                                <div class="wpbs-label-description"><?php echo __('Select how to apply the tax. To all pricing items (calendar price per day and form product fields) or to calendar price only (calendar price per day).', 'wp-booking-system') ?></div>
                            </div>

                            <!-- Applicable Period -->
                            <div class="wpbs-form-field">
                                <label class="wpbs-label" for="">
                                    <?php echo __('Applicable Period', 'wp-booking-system'); ?>
                                </label>

                                <div class="wpbs-input wpbs-has-columns">
                                    <div class="wpbs-half wpbs-tax-datepicker-wrap">
                                        <label><?php echo __('Start date', 'wp-booking-system'); ?></label>
                                        <input type="text" class="wpbs-tax-datepicker" value="<?php echo (!empty($settings['payment_tax_start_period_display_value'][$i]) ? esc_attr($settings['payment_tax_start_period_display_value'][$i]) : ''); ?>" name="wpbs_settings[payment_tax_start_period_display_value][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_start_period_display_value][id]">
                                        <input type="hidden" class="wpbs-tax-datepicker-timestamp" value="<?php echo (!empty($settings['payment_tax_start_period'][$i]) && !empty($settings['payment_tax_start_period_display_value'][$i]) ? esc_attr($settings['payment_tax_start_period'][$i]) : ''); ?>" name="wpbs_settings[payment_tax_start_period][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_start_period][id]">
                                    </div>
                                    <div class="wpbs-half wpbs-tax-datepicker-wrap">
                                        <label><?php echo __('End date', 'wp-booking-system'); ?></label>
                                        <input type="text" class="wpbs-tax-datepicker" value="<?php echo (!empty($settings['payment_tax_end_period_display_value'][$i]) ? esc_attr($settings['payment_tax_end_period_display_value'][$i]) : ''); ?>" name="wpbs_settings[payment_tax_end_period_display_value][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_end_period_display_value][id]">
                                        <input type="hidden" class="wpbs-tax-datepicker-timestamp" value="<?php echo (!empty($settings['payment_tax_end_period'][$i]) && !empty($settings['payment_tax_end_period_display_value'][$i]) ? esc_attr($settings['payment_tax_end_period'][$i]) : ''); ?>" name="wpbs_settings[payment_tax_end_period][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_end_period][id]">
                                    </div>
                                </div>

                                <div class="wpbs-label-description"><?php echo __('Optional. The period in which the tax is applied.', 'wp-booking-system'); ?></div>

                            </div>

                            <!-- Validity Inclusion -->
                            <div class="wpbs-form-field ">
                                <label class="wpbs-label" for="payment_tax_inclusion">
                                    <?php echo __('Period Inclusion', 'wp-booking-system'); ?>
                                </label>
                                <div class="wpbs-input">
                                    <select name="wpbs_settings[payment_tax_inclusion][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_inclusion][id]">
                                        <option <?php isset($settings['payment_tax_inclusion'][$i]) ? selected('start_date', $settings['payment_tax_inclusion'][$i]) : '' ?> value="start_date"><?php echo __('Start Date only', 'wp-booking-system'); ?></option>
                                        <option <?php isset($settings['payment_tax_inclusion'][$i]) ? selected('any_date', $settings['payment_tax_inclusion'][$i]) : '' ?> value="any_date"><?php echo __('Start Date or End Date', 'wp-booking-system'); ?></option>
                                        <option <?php isset($settings['payment_tax_inclusion'][$i]) ? selected('entire_date', $settings['payment_tax_inclusion'][$i]) : '' ?> value="entire_date"><?php echo __('Start Date and End Date', 'wp-booking-system'); ?></option>
                                    </select>
                                </div>

                                <div class="wpbs-label-description"><?php echo __('Choose to apply the tax only if the start date is contained in the Applicable Period, or the entire date selection is contained in the Applicable Period.', 'wp-booking-system') ?></div>
                            </div>

                            <!-- Multiplication -->
                            <div class="wpbs-form-field wpbs-settings-tax-calendars">
                                <label class="wpbs-label" for="payment_tax_multiplication">
                                    <?php echo __('Multiplication', 'wp-booking-system'); ?>
                                </label>

                                <div class="wpbs-input">
                                    <select name="wpbs_settings[payment_tax_multiplication][<?php echo $i; ?>]" data-name="wpbs_settings[payment_tax_multiplication][id]">
                                        <option value="">-</option>
                                        <?php foreach ($fields_dropdown as $form): ?>
                                            <optgroup label="<?php echo $form['form_name']; ?>">
                                                <?php foreach ($form['fields'] as $field_id => $field_name): ?>
                                                    <option value="<?php echo $field_id ?>" <?php echo isset($settings['payment_tax_multiplication'][$i]) && $settings['payment_tax_multiplication'][$i] == $field_id ? 'selected' : ''; ?>><?php echo $field_name; ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="wpbs-label-description"><?php echo __('Multiply the tax by the value of a form field. This will only work when making bookings using the form that the selected field belongs to.', 'wp-booking-system') ?></div>
                            </div>

                            <!-- Calendars -->
                            <div class="wpbs-form-field wpbs-settings-tax-calendars">
                                <label class="wpbs-label" for="payment_tax_calendars">
                                    <?php echo __('Calendars', 'wp-booking-system'); ?>
                                </label>

                                <div class="wpbs-input wpbs-chosen-wrapper">
                                    <select name="wpbs_settings[payment_tax_calendars][<?php echo $i; ?>][]" data-name="wpbs_settings[payment_tax_calendars][id][]" class="wpbs-chosen" multiple>
                                        <?php foreach ($calendars as $calendar): ?>
                                            <option value="<?php echo $calendar->get('id'); ?>" <?php echo isset($settings['payment_tax_calendars'][$i]) && in_array($calendar->get('id'), $settings['payment_tax_calendars'][$i]) ? 'selected' : ''; ?>><?php echo $calendar->get('name'); ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="wpbs-label-description"><?php echo __('Select the calendars the tax applies to. If no calendars are selected, the tax will be applied to all calendars.', 'wp-booking-system') ?></div>
                            </div>

                            <!-- Forms -->
                            <div class="wpbs-form-field wpbs-settings-tax-forms">
                                <label class="wpbs-label" for="payment_tax_forms">
                                    <?php echo __('Forms', 'wp-booking-system'); ?>
                                </label>

                                <div class="wpbs-input wpbs-chosen-wrapper">
                                    <select name="wpbs_settings[payment_tax_forms][<?php echo $i; ?>][]" data-name="wpbs_settings[payment_tax_forms][id][]" class="wpbs-chosen" multiple>
                                        <?php foreach ($forms as $form): ?>
                                            <option value="<?php echo $form->get('id'); ?>" <?php echo isset($settings['payment_tax_forms'][$i]) && in_array($form->get('id'), $settings['payment_tax_forms'][$i]) ? 'selected' : ''; ?>><?php echo $form->get('name'); ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="wpbs-label-description"><?php echo __('Select the forms the tax applies to. If no forms are selected, the tax will be applied to all forms.', 'wp-booking-system') ?></div>
                            </div>

                            <a href="#" class="wpbs-secondary-button  wpbs-remove-button wpbs-settings-tax-remove" title="<?php echo __('Remove Tax', 'wp-booking-system') ?>"><i class="wpbs-icon-trash"></i> <?php echo __('Remove Tax', 'wp-booking-system') ?></a>
                        </div>

                    </div>
                <?php endfor; ?>
            </div>

            <a href="#" class="wpbs-secondary-button wpbs-settings-tax-add"><?php echo __('Add Tax', 'wp-booking-system') ?></a>

        </div>

    </div>

    <div class="wpbs-divider"></div>
    <h2>
        <?php echo __('VAT', 'wp-booking-system'); ?>
        <small class="wpbs-heading-description"><?php echo __('Enabling this will deduct the VAT percentage from all your prices and add it back to the pricing table after the Subtotal is displayed.', 'wp-booking-system') ?></small>
    </h2>

    <!-- Enable VAT -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="payment_vat_enable">
            <?php echo __('Enable VAT', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <label for="payment_vat_enable" class="wpbs-checkbox-switch">
                <input type="hidden" name="wpbs_settings[payment_vat_enable]" value="0">
                <input data-target="#wpbs-vat-wrapper" name="wpbs_settings[payment_vat_enable]" type="checkbox" id="payment_vat_enable" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($settings['payment_vat_enable'])) ? 'checked' : ''; ?>>
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>
    </div>

    <div id="wpbs-vat-wrapper" class="wpbs-payment-vat-wrapper wpbs-form-fields-indent wpbs-settings-wrapper <?php echo (!empty($settings['payment_vat_enable'])) ? 'wpbs-settings-wrapper-show' : ''; ?>">

        <!-- VAT Percentage -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="payment_vat_percentage">
                <?php echo __('VAT Percentage', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <div class="wpbs-input-wrapper">
                    <span class="wpbs-input-symbol wpbs-input-symbol-left"><span>%</span></span>
                    <input name="wpbs_settings[payment_vat_percentage]" id="payment_vat_percentage" type="number" min="0" step="0.01" value="<?php echo (!empty($settings['payment_vat_percentage']) ? esc_attr($settings['payment_vat_percentage']) : ''); ?>" />
                </div>
            </div>
        </div>

        <!-- VAT Name -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="payment_vat_name">
                <?php echo __('VAT Name', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <input name="wpbs_settings[payment_vat_name]" id="payment_vat_name" type="text" value="<?php echo (!empty($settings['payment_vat_name']) ? esc_attr($settings['payment_vat_name']) : 'VAT'); ?>" />
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
                                    <input name="wpbs_settings[payment_vat_name_translation_<?php echo $language; ?>]" type="text" id="payment_vat_name_translation_<?php echo $language; ?>" value="<?php echo (!empty($settings['payment_vat_name_translation_' . $language])) ? esc_attr($settings['payment_vat_name_translation_' . $language]) : ''; ?>" >
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="wpbs-form-field">
            <label class="wpbs-label" for="payment_vat_display_only">
                <?php echo __('Display Only', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <label for="payment_vat_display_only" class="wpbs-checkbox-switch">
                    <input type="hidden" name="wpbs_settings[payment_vat_display_only]" value="0">
                    <input name="wpbs_settings[payment_vat_display_only]" type="checkbox" id="payment_vat_display_only" class="wpbs-settings-toggle" <?php echo (!empty($settings['payment_vat_display_only'])) ? 'checked' : ''; ?>>
                    <div class="wpbs-checkbox-slider"></div>
                </label>
            </div>

            <div class="wpbs-label-description"><?php echo __('Only display the VAT value in the pricing table without subtracting it from the line items. ', 'wp-booking-system') ?></div>
        </div>

    </div>
</div>