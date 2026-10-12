<div class="wpbs-dynamic-tags-wrapper">

    <!-- Email Tags -->
    <div class="wpbs-dynamic-tags">
        <h2 class="title"><?php echo __('Dynamic Tags', 'wp-booking-system-invoices'); ?></h2>
        <p><?php echo __('You can use these dynamic tags in the Confirmation Message field. They will be replaced with the values submitted in the form.', 'wp-booking-system-invoices'); ?></p>

        <?php wpbs_output_email_tags($form_data); ?>
    </div>

    <!-- Dynamic Tags Content -->
    <div class="wpbs-tab-inner">
        <h2><?php echo __('Form Confirmation', 'wp-booking-system'); ?></h2>

        <!-- Form Changed Notice -->
        <div class="wpbs-page-notice notice-info wpbs-form-changed-notice">
            <p><?php echo __('It appears you made changes to the form. Make sure you save the form before you make any changes on this page to ensure all email tags are up to date.', 'wp-booking-system-invoices'); ?></p>
        </div>

        <!-- Confirmation Type -->
        <?php $confirmation_type = (!empty($form_meta['form_confirmation_type'][0])) ? esc_attr($form_meta['form_confirmation_type'][0]) : ''; ?>
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="form_confirmation_type"><?php echo __('Confirmation Type', 'wp-booking-system'); ?></label>

            <div class="wpbs-input">
                <select name="form_confirmation_type" id="form_confirmation_type">
                    <option <?php echo ($confirmation_type == 'message') ? 'selected' : ''; ?> value="message"><?php echo __('Show a Message', 'wp-booking-system'); ?></option>
                    <option <?php echo ($confirmation_type == 'redirect') ? 'selected' : ''; ?> value="redirect"><?php echo __('Redirect to a Page', 'wp-booking-system'); ?></option>
                </select>
            </div>
        </div>

        <div class="wpbs-form-fields-indent <?php echo (empty($confirmation_type) || $confirmation_type == 'message') ? 'wpbs-confirmation-type-show' : ''; ?> wpbs-confirmation-type wpbs-confirmation-type-message">
            <!-- Message -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="form_confirmation_message">
                    <?php echo __('Message', 'wp-booking-system'); ?>
                </label>

                <div class="wpbs-input">
                    <?php wp_editor((!empty($form_meta['form_confirmation_message'][0])) ? $form_meta['form_confirmation_message'][0] : '', 'form_confirmation_message', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false)) ?>
                </div>

                <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-link-button wpbs-form-translations-toggle"><?php echo __('Translations', 'wp-booking-system'); ?> <i class="wpbs-icon-angle-down"></i></a><?php endif ?>

                <?php if (wpbs_translations_active()): ?>
                    <div class="wpbs-form-translations wpbs-form-fields-indent">
                        <?php foreach ($active_languages as $language): ?>
                            <div class="wpbs-form-field wpbs-absolute-label">
                                <label class="wpbs-label"><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo $languages[$language]; ?></label>
                                <div class="wpbs-input">
                                    <?php wp_editor((!empty($form_meta['form_confirmation_message_translation_' . $language][0])) ? $form_meta['form_confirmation_message_translation_' . $language][0] : '', 'form_confirmation_message_translation_' . $language, array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false)) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <div class="wpbs-form-fields-indent <?php echo ($confirmation_type == 'redirect') ? 'wpbs-confirmation-type-show' : ''; ?> wpbs-confirmation-type wpbs-confirmation-type-redirect">

            <!-- Redirect URL -->
            <div class="wpbs-form-field ">
                <label class="wpbs-label" for="form_confirmation_redirect_url">
                    <?php echo __('Redirect URL', 'wp-booking-system'); ?>
                </label>

                <div class="wpbs-input">
                    <input name="form_confirmation_redirect_url" type="text" id="form_confirmation_redirect_url" value="<?php echo (!empty($form_meta['form_confirmation_redirect_url'][0])) ? esc_attr($form_meta['form_confirmation_redirect_url'][0]) : ''; ?>" >
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
                                        <input name="form_confirmation_redirect_url_translation_<?php echo $language; ?>" type="text" id="form_confirmation_redirect_url_translation_<?php echo $language; ?>" value="<?php echo (!empty($form_meta['form_confirmation_redirect_url_translation_' . $language][0])) ? esc_attr($form_meta['form_confirmation_redirect_url_translation_' . $language][0]) : ''; ?>" >
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

</div>