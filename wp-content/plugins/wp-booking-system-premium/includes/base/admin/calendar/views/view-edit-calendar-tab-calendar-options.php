<div class="wpbs-tab-inner">

    <?php

    /**
     * Action hook to add extra form fields to the main calendar edit area
     *
     * @param WPBS_Calendar $calendar
     *
     */
    do_action('wpbs_submenu_page_calendars_tab_calendar_options_top', $calendar);

    ?>

    <div class="wpbs-divider"></div>
    <h2>
        <?php echo __('Link Calendar', 'wp-booking-system'); ?>
        <small class="wpbs-heading-description"><?php echo __("You can attach a post/page from your WordPress website or an external link to this calendar. If the calendar has a link attached, everywhere the calendar's title is displayed, the link will automatically be added to the title.", 'wp-booking-system'); ?></small>
    </h2>



    <div class="wpbs-form-field">

        <label class="wpbs-label"><?php echo __('Link Type', 'wp-booking-system'); ?></label>

        <div class="wpbs-input">

            <?php $calendar_link_type = wpbs_get_calendar_meta($calendar->get('id'), 'calendar_link_type', true); ?>

            <select name="calendar_link_type">
                <option value="internal" <?php selected($calendar_link_type, 'internal', true); ?>><?php echo __('Internal', 'wp-booking-system'); ?></option>
                <option value="external" <?php selected($calendar_link_type, 'external', true); ?>><?php echo __('External', 'wp-booking-system'); ?></option>
            </select>
        </div>

    </div>


    <!-- Actual Link -->
    <div class="wpbs-form-fields-indent" id="wpbs-settings-field-wrapper-calendar-link-internal">


        <!-- Internal Link -->
        <?php $post_types_dropdown = wpbs_get_post_types_as_dropdown(); ?>
        <div class="wpbs-form-field">

            <label class="wpbs-label"><?php echo __('Select Internal Link', 'wp-booking-system'); ?></label>

            <div class="wpbs-input">

                <?php $calendar_link_internal = wpbs_get_calendar_meta($calendar->get('id'), 'calendar_link_internal', true); ?>

                <select name="calendar_link_internal">
                    <option value=""><?php echo __('Select...', 'wp-booking-system'); ?></option>
                    <?php foreach ($post_types_dropdown as $post_type => $posts): ?>
                        <optgroup label="<?php echo $post_type; ?>">
                            <?php foreach ($posts as $post_id => $post_title): ?>
                                <option value="<?php echo $post_id; ?>" <?php selected($calendar_link_internal, $post_id); ?>><?php echo $post_title; ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>

            </div>

            <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-link-button wpbs-form-translations-toggle"><?php echo __('Translations', 'wp-booking-system'); ?> <i class="wpbs-icon-angle-down"></i></a><?php endif ?>

            <?php if (wpbs_translations_active()): ?>
                <!-- Required Field Translations -->
                <div class="wpbs-form-translations wpbs-form-fields-indent">
                    <?php foreach ($active_languages as $language): ?>
                        <?php $calendar_link_internal_translation = wpbs_get_calendar_meta($calendar->get('id'), 'calendar_link_internal_translation_' . $language, true); ?>
                        <div class="wpbs-form-field wpbs-small-margin">
                            <div class="wpbs-input">
                                <div class="wpbs-input-wrapper">
                                    <span class="wpbs-input-symbol wpbs-input-symbol-left wpbs-flag-symbol">
                                        <span><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo strtoupper($language); ?></span>
                                    </span>

                                    <select name="calendar_link_internal_translation_<?php echo $language; ?>" id="calendar_link_internal_translation_<?php echo $language; ?>">
                                        <option value=""><?php echo __('Select...', 'wp-booking-system'); ?></option>
                                        <?php foreach ($post_types_dropdown as $post_type => $posts): ?>
                                            <optgroup label="<?php echo $post_type; ?>">
                                                <?php foreach ($posts as $post_id => $post_title): ?>
                                                    <option value="<?php echo $post_id; ?>" <?php selected($calendar_link_internal_translation, $post_id); ?>><?php echo $post_title; ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif ?>


        </div>
    </div>
    
    <div class="wpbs-form-fields-indent" id="wpbs-settings-field-wrapper-calendar-link-external">
        <!-- External Link -->
        <div class="wpbs-form-field">

            <label class="wpbs-label"><?php echo __('Add External Link', 'wp-booking-system'); ?></label>


            <div class="wpbs-input">

                <?php $calendar_link_external = wpbs_get_calendar_meta($calendar->get('id'), 'calendar_link_external', true); ?>

                <input type="text" placeholder="https://..." name="calendar_link_external" value="<?php echo esc_attr($calendar_link_external); ?>" />
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
                                    <input placeholder="https://..." name="calendar_link_external_translation_<?php echo $language; ?>" type="text" id="calendar_link_external_translation_<?php echo $language; ?>" value="<?php echo esc_attr(wpbs_get_calendar_meta($calendar->get('id'), 'calendar_link_external_translation_' . $language, true)); ?>" >
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif ?>

        </div>

    </div>



    <?php

    /**
     * Action hook to add extra form fields to the main calendar edit area
     *
     * @param WPBS_Calendar $calendar
     *
     */
    do_action('wpbs_submenu_page_calendars_tab_calendar_options_bottom', $calendar);

    ?>

</div>