<div class="wpbs-form-builder-wrapper">

    <!-- Form Builder -->
    <div class="wpbs-form-builder">
        <div id="wpbs-form-builder" class="<?php echo (wpbs_get_recaptcha_keys() !== false) ? 'hide-notice_captcha' : ''; ?>"><!-- --></div>
        <input type="hidden" id="wpbs_form_field_id_index" name="wpbs_form_field_id_index" value="<?php echo (!empty($form_meta['wpbs_form_field_id_index'][0])) ? esc_attr($form_meta['wpbs_form_field_id_index'][0]) : '1'; ?>">
        <input type="hidden" name="form_fields[exists]" value="yes" />
        <p class="wpbs-start-building">
            <i class="dashicons dashicons-info-outline"></i>
            <?php echo __('Start building your form by adding a form element.', 'wp-booking-system') ?><br>
            <?php echo __('Click on any of the elements on the right to add them here!', 'wp-booking-system') ?>
        </p>

        <?php

        /**
         * Action hook to add extra form fields to the main form edit area
         *
         * @param WPBS_Form $form
         *
         */
        do_action('wpbs_view_edit_form_main', $form);

        ?>

    </div>


    <!-- Form Builder Elements -->
    <div class="wpbs-form-builder-elements">
        <h2><?php echo __('Form Elements', 'wp-booking-system') ?></h2>

        <p class="wpbs-hide-on-tablet-landscape"><?php echo __('Click on a form element to add it to the form builder.', 'wp-booking-system') ?></p>

        <div class="wpbs-show-on-tablet-landscape">
            <a href="#" class="wpbs-primary-button wpbs-js-add-form-element"><?php echo __('Add Element', 'wp-booking-system') ?></a>
        </div>

        <div class="wpbs-form-builder-elements-wrapper">
            <div class="wpbs-form-builder-elements-container">
                <a href="#" id="wpbs-form-builder-modal-close"><i class="wpbs-icon-close"></i></a>

                <?php foreach (wpbs_form_available_field_types_groups() as $group => $group_name): ?>
                    <div class="wpbs-form-builder-elements-group">
                        <!-- Form Fields -->
                        <h4><?php echo $group_name ?></h4>
                        <div class="wpbs-form-builder-add-form-fields-wrapper">
                            <div class="wpbs-form-builder-add-form-fields">
                                <?php foreach ($available_field_types as $form_field): ?>
                                    <?php if (isset($form_field['group']) && $form_field['group'] != $group) continue; ?>

                                    <a href="#" data-field-type="<?php echo $form_field['type']; ?>" class="wpbs-secondary-button">
                                        <i class="wpbs-icon-<?php echo $form_field['type']; ?>"></i>
                                        <span><?php echo str_replace('_', ' ', $form_field['type']) ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php
        /**
         * Action hook to add extra form fields to the main form edit area
         *
         * @param WPBS_Form $form
         *
         */
        do_action('wpbs_view_edit_form_sidebar', $form);
        ?>

    </div><!-- / Sidebar Content -->

</div><!-- / #post-body -->