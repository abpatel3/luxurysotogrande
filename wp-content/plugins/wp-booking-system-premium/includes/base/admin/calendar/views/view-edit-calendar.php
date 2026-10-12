<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$calendar_id = absint(!empty($_GET['calendar_id']) ? $_GET['calendar_id'] : 0);
$calendar = wpbs_get_calendar($calendar_id);

if (is_null($calendar)) {
    return;
}

$current_year = (!empty($_GET['year']) ? absint($_GET['year']) : current_time('Y'));
$current_month = (!empty($_GET['month']) ? absint($_GET['month']) : current_time('n'));

$settings = get_option('wpbs_settings', array());

$removable_query_args = wp_removable_query_args();

$legend_items = wpbs_get_legend_items(array('calendar_id' => $calendar_id));

$languages = wpbs_get_languages();
$active_languages = (! empty($settings['active_languages']) ? $settings['active_languages'] : array());

?>

<?php if (isset($_GET['booking_id']) && !empty($_GET['booking_id'])): ?>
    <script>
        jQuery(document).ready(function() {
            wpbs_open_booking_modal(<?php echo $_GET['booking_id']; ?>, '<?php echo isset($_GET['modal-tab']) ? esc_attr($_GET['modal-tab']) : ''; ?>');
        });
    </script>
<?php endif; ?>

<?php if (isset($_GET['add_booking'])): ?>
    <script>
        jQuery(document).ready(function() {
            jQuery('#wpbs-add-booking-open-modal').trigger('click');
        });
    </script>
<?php endif; ?>

<div class="wrap wpbs-wrap wpbs-wrap-edit-calendar">

    <?php wpbs_get_wp_admin_header(); ?>

    <form method="POST" action="" autocomplete="off">

        <!-- Plugin Sticky Header -->
        <div class="wpbs-plugin-sticky-header">

            <!-- Page Title -->
            <span class="wpbs-page-heading">
                <?php echo __('Edit Calendar', 'wp-booking-system'); ?>

                <!-- Calendar Title -->
                <div id="titlediv" class="wpbs-page-heading-editable">
                    <div id="titlewrap">
                        <div class="wpbs-form-field">
                            <div class="wpbs-input">
                                <input type="text" name="calendar_name" size="30" value="<?php echo esc_attr($calendar->get('name')) ?>">
                            </div>
                        </div>

                        <?php if (isset($settings['active_languages']) && count($settings['active_languages']) > 0) : ?>
                            <a href="#" class="wpbs-secondary-button titlewrap-toggle"><?php echo __('Translations', 'wp-booking-system') ?></a>
                        <?php endif ?>

                    </div>
                </div>

                <span class="wpbs-heading-tag"><strong><?php printf(__('Calendar ID: %d', 'wp-booking-system'), $calendar_id); ?></strong></span>
            </span>

            <!-- Buttons -->
            <div class="wpbs-header-buttons">

                <!-- Back Button -->
                <a href="<?php echo add_query_arg(array('page' => 'wpbs-calendars'), admin_url('admin.php')); ?>" class="wpbs-secondary-button"><?php echo __('Back to all calendars', 'wp-booking-system') ?></a>
                <a href="#" class="wpbs-primary-button wpbs-save-calendar"><?php echo __('Save Changes', 'wp-booking-system'); ?></a>

            </div>

            <?php if (isset($settings['active_languages']) && count($settings['active_languages']) > 0) : ?>
                <div class="titlewrap-translations">
                    <div class="wpbs-form-translations wpbs-form-fields-indent">
                        <?php foreach ($settings['active_languages'] as $language) : ?>

                            <div class="wpbs-form-field wpbs-small-margin">
                                <div class="wpbs-input">
                                    <div class="wpbs-input-wrapper">
                                        <span class="wpbs-input-symbol wpbs-input-symbol-left wpbs-flag-symbol">
                                            <span><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo strtoupper($language); ?></span>
                                        </span>
                                        <input type="text" name="calendar_name_translation_<?php echo $language; ?>" size="30" value="<?php echo esc_attr(wpbs_get_calendar_meta($calendar->get('id'), 'calendar_name_translation_' . $language, true)) ?>">
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- WP Notifications -->
        <div class="wpbs-notifications">
            <h1 class="wp-heading-inline"></h1>
        </div>

        <div class="wpbs-plugin-wrapper">

            <?php if (wpbs_check_for_disabled_ical_feeds($calendar_id)): ?>
                <div class="wpbs-page-notice notice-warning">
                    <p><?php echo sprintf(__('One or more of your iCalendar feeds are disabled. %sClick here to fix this.%s', 'wp-booking-system'), '<a href="' . add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'ical-import-export', 'calendar_id' => $calendar_id), admin_url('admin.php')) . '">', '</a>') ?></p>
                </div>
            <?php endif; ?>


            <?php
            /**
             *  Bookings
             * 
             */
            ?>

            <div class="wpbs-box">

                <div class="wpbs-box-header">
                    <h3><?php echo __('Bookings', 'wp-booking-system'); ?></h3>
                </div>

                <div id="wpbs-bookings-wrap">
                    <?php
                    $bookings_outputter = new WPBS_Bookings_Outputter($calendar_id);
                    $bookings_outputter->display();
                    ?>
                </div>

            </div>

            <div class="wpbs-edit-calendar-wrapper">
                <!-- Sidebar Content -->
                <div class="wpbs-edit-calendar-sidebar">

                    <!-- Calendar -->
                    <div class="wpbs-box wpbs-calendar-box">

                        <div class="wpbs-box-content wpbs-no-padding">

                            <?php
                            $calendar_args = array(
                                'current_year' => $current_year,
                                'current_month' => $current_month,
                                'start_weekday' => (!empty($settings['backend_start_day']) ? (int) $settings['backend_start_day'] : 1),
                                'show_title' => 0,
                                'show_legend' => 0,
                                'is_admin' => true
                            );

                            $calendar_args = apply_filters('wpbs_edit_calendar_outputter_args', $calendar_args, $calendar);

                            $calendar_outputter = new WPBS_Calendar_Outputter($calendar, $calendar_args);
                            $calendar_outputter->display();
                            ?>

                        </div>
                    </div><!-- / Calendar -->

                    <!-- Availability Bulk Edit -->
                    <div id="wpbs-bulk-edit-availability-wrapper" class="wpbs-box">

                        <div class="wpbs-box-header">
                            <h3><?php echo __('Bulk Edit Availability', 'wp-booking-system'); ?></h3>
                        </div>

                        <div class="wpbs-box-content">

                            <!-- Start Date -->
                            <div class="wpbs-form-field wpbs-small-margin">
                                <label class="wpbs-label" for="wpbs-bulk-edit-availability-start-date"><?php echo __('Start Date', 'wp-booking-system'); ?></label>
                                <div class="wpbs-input">
                                    <input id="wpbs-bulk-edit-availability-start-date" type="text" class="wpbs-datepicker" placeholder="YYYY-MM-DD" readonly />
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="wpbs-form-field wpbs-small-margin">
                                <label class="wpbs-label" for="wpbs-bulk-edit-availability-end-date"><?php echo __('End Date', 'wp-booking-system'); ?></label>
                                <div class="wpbs-input">
                                    <input id="wpbs-bulk-edit-availability-end-date" type="text" class="wpbs-datepicker" placeholder="YYYY-MM-DD" readonly />
                                </div>
                            </div>

                            <!-- Week Days -->
                            <div class="wpbs-form-field wpbs-small-margin">
                                <label class="wpbs-label" for="wpbs-bulk-edit-availability-week-days">
                                    <?php echo __('Week Days', 'wp-booking-system'); ?>
                                </label>
                                <div class="wpbs-input wpbs-has-columns">
                                    <div class="wpbs-full">
                                        <div class="wpbs-weekday-checkboxes">

                                            <?php $start_weekday = (!empty($settings['backend_start_day']) ? (int) $settings['backend_start_day'] : 1); ?>

                                            <?php for ($i = $start_weekday; $i < ($start_weekday + 7); $i++): ?>
                                                <div class="wpbs-weekday-checkbox">
                                                    <?php $week_day_letter = wpbs_get_days_first_letters(wpbs_get_locale())[($i + 6) % 7]; ?>

                                                    <?php $week_day = $i % 7; ?>
                                                    <input type="checkbox" id="wpbs-bulk-edit-availability-week-day-<?php echo $week_day; ?>" name="wpbs-bulk-edit-availability-week-day" class="wpbs-bulk-edit-availability-week-day" value="<?php echo $week_day; ?>" />
                                                    <label for="wpbs-bulk-edit-availability-week-day-<?php echo $week_day; ?>" class="wpbs-bulk-edit-availability-week-days">
                                                        <span><?php echo $week_day_letter; ?></span>
                                                    </label>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="wpbs-label-description">
                                    <?php echo __('Only select specific week days from the date range chosen above. Leave empty to select all days.', 'wp-booking-system'); ?>
                                </div>
                            </div>

                            <!-- Legend Item -->
                            <div class="wpbs-form-field wpbs-small-margin">
                                <label class="wpbs-label" for="wpbs-bulk-edit-availability-legend-item"><?php echo __('Legend Item', 'wp-booking-system'); ?></label>
                                <div class="wpbs-input">
                                    <select id="wpbs-bulk-edit-availability-legend-item">
                                        <option value=""></option>
                                        <?php
                                        foreach ($legend_items as $legend_item) {

                                            echo '<option value="' . esc_attr($legend_item->get('id')) . '">' . $legend_item->get_name(wpbs_get_locale()) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <?php

                            /**
                             * Action hook to add extra form fields to the bulk editor
                             *
                             */
                            do_action('wpbs_view_edit_calendar_bulk_editor_before');

                            ?>

                            <!-- Description -->
                            <div class="wpbs-form-field wpbs-small-margin">
                                <label class="wpbs-label" for="wpbs-bulk-edit-availability-description"><?php echo __('Description', 'wp-booking-system'); ?></label>
                                <div class="wpbs-input">
                                    <input id="wpbs-bulk-edit-availability-description" type="text" />
                                </div>
                            </div>

                            <!-- Tooltip -->
                            <div class="wpbs-form-field wpbs-small-margin">
                                <label class="wpbs-label" for="wpbs-bulk-edit-availability-tooltip"><?php echo __('Tooltip', 'wp-booking-system'); ?></label>
                                <div class="wpbs-input">
                                    <input id="wpbs-bulk-edit-availability-tooltip" type="text" />
                                </div>
                            </div>

                            <!-- Overwrite -->
                            <div class="wpbs-form-field wpbs-small-margin">
                                <div class="wpbs-label"><?php echo __('Ignore empty fields', 'wp-booking-system'); ?></div>
                                <div class="wpbs-input">
                                    <label for="wpbs-bulk-edit-availability-overwrite" class="wpbs-bulk-edit-availability-overwrite wpbs-checkbox-switch">
                                        <input id="wpbs-bulk-edit-availability-overwrite" type="checkbox" checked class="wpbs-settings-toggle" />
                                        <div class="wpbs-checkbox-slider"></div>
                                    </label>
                                </div>
                                <div class="wpbs-label-description">
                                    <?php echo __('If this box is ticked, the empty fields in the bulk editor will be ignored and will not clear existing data from the calendar.', 'wp-booking-system'); ?>
                                </div>
                            </div>

                        </div>

                        <div class="wpbs-box-footer-wrapper">
                            <div class="wpbs-box-footer">
                                <a id="wpbs-bulk-edit-availability" class="wpbs-secondary-button wpbs-float-right" href="#"><?php echo __('Bulk Edit', 'wp-booking-system'); ?></a>
                                <a id="wpbs-bulk-edit-availability-undo" class="wpbs-inactive" href="#"><?php echo __('Undo', 'wp-booking-system'); ?></a>
                            </div>
                        </div>

                    </div><!-- / Availability Bulk Edit -->



                    <!-- Calendar Legend -->
                    <div class="wpbs-box">

                        <div class="wpbs-box-header">
                            <h3><?php echo __('Legend', 'wp-booking-system'); ?></h3>
                        </div>

                        <div class="wpbs-box-content">

                            <?php


                            foreach ($legend_items as $legend_item) {

                                echo '<div class="wpbs-legend-item">';
                                echo wpbs_get_legend_item_icon($legend_item->get('id'), $legend_item->get('type'), $legend_item->get('color'));
                                echo '<span class="wpbs-legend-item-name">' . $legend_item->get_name(wpbs_get_locale()) . '</span>';
                                echo '</div>';
                            }
                            ?>

                        </div>

                        <div class="wpbs-box-footer-wrapper">
                            <div class="wpbs-box-footer">
                                <a class="wpbs-secondary-button" href="<?php echo add_query_arg(array('page' => 'wpbs-calendars', 'calendar_id' => $calendar->get('id'), 'subpage' => 'view-legend'), admin_url('admin.php')); ?>"><?php echo __('Edit Legend Items', 'wp-booking-system'); ?></a>
                            </div>
                        </div>

                    </div><!-- / Calendar Legend -->

                    <!-- iCal Export -->
                    <div class="wpbs-box">

                        <div class="wpbs-box-header">
                            <h3><?php echo __('iCal Import/Export', 'wp-booking-system'); ?></h3>
                        </div>

                        <div class="wpbs-box-content">

                            <p><?php echo __('To configure the iCal import & export settings and have access to the iCal export link please click the button below.', 'wp-booking-system'); ?></p>

                            <a href="<?php echo add_query_arg(array('page' => 'wpbs-calendars', 'calendar_id' => $calendar->get('id'), 'subpage' => 'ical-import-export'), admin_url('admin.php')); ?>" class="wpbs-secondary-button"><span class="dashicons dashicons-upload"></span> <?php echo __('iCal Import/Export', 'wp-booking-system'); ?></a>

                        </div>

                    </div><!-- / iCal Export -->

                    <!-- CSV Export -->
                    <div class="wpbs-box">

                        <div class="wpbs-box-header">
                            <h3><?php echo __('CSV Export', 'wp-booking-system'); ?></h3>
                        </div>

                        <div class="wpbs-box-content">

                            <p><?php echo __('To configure the CSV export settings please click the button below. ', 'wp-booking-system'); ?></p>

                            <a href="<?php echo add_query_arg(array('subpage' => 'csv-export'), remove_query_arg($removable_query_args)); ?>" class="wpbs-secondary-button"><span class="dashicons dashicons-media-spreadsheet"></span> <?php echo __('CSV Export', 'wp-booking-system'); ?></a>

                        </div>

                    </div><!-- / CSV Export -->



                </div><!-- / Sidebar Content -->

                <!-- Main Post Body Content -->
                <div class="wpbs-edit-calendar-main-content">

                    <!-- Availability -->
                    <div class="wpbs-box">

                        <?php
                        $tabs = $this->get_tabs();
                        $active_tab = (! empty($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'edit-dates'); ?>

                        <!-- Tabs Nav -->
                        <div class="wpbs-tabs-nav">
                            <nav class="wpbs-tabs-menu">
                                <ul>
                                    <?php

                                    if (! empty($tabs)) {
                                        foreach ($tabs as $tab_slug => $tab_name) {

                                            echo '<li><a href="' . add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'edit-calendar', 'calendar_id' => $calendar_id, 'tab' => $tab_slug), admin_url('admin.php')) . '" data-tab="' . $tab_slug . '" class="wpbs-nav-tab ' . ($active_tab == $tab_slug ? 'wpbs-nav-tab-active' : '') . '">' . $tab_name . '</a></li>';
                                        }
                                    }

                                    ?>

                                    <li><a class="wpbs-nav-tab-external" href="<?php echo add_query_arg(array('page' => 'wpbs-calendars', 'calendar_id' => $calendar->get('id'), 'subpage' => 'ical-import-export'), admin_url('admin.php')); ?>"><?php echo __('iCal Import/Export', 'wp-booking-system') ?><i class="dashicons dashicons-external"></i></a></li>

                                    <li><a class="wpbs-nav-tab-external" href="<?php echo add_query_arg(array('page' => 'wpbs-calendars', 'calendar_id' => $calendar->get('id'), 'subpage' => 'view-legend'), admin_url('admin.php')); ?>"><?php echo __('Edit Legend Items', 'wp-booking-system') ?><i class="dashicons dashicons-external"></i></a></li>

                                    <li><a class="wpbs-nav-tab-external" href="<?php echo add_query_arg(array('page' => 'wpbs-calendars', 'calendar_id' => $calendar->get('id'), 'subpage' => 'csv-export'), admin_url('admin.php')); ?>"><?php echo __('Calendar CSV Export', 'wp-booking-system') ?><i class="dashicons dashicons-external"></i></a></li>

                                </ul>
                            </nav>
                        </div>

                        <div class="wpbs-box-content">
                            <div class="wpbs-tab-wrapper">

                                <?php

                                if (! empty($tabs)) {

                                    foreach ($tabs as $tab_slug => $tab_name) {


                                        echo '<div class="wpbs-tab wpbs-tab-' . $tab_slug . ' ' . ($active_tab == $tab_slug ? 'wpbs-active' : '') . '" data-tab="' . $tab_slug . '">';
                                        // Handle general tab
                                        if ($tab_slug == 'edit-dates') {

                                            include 'view-edit-calendar-tab-edit-dates.php';

                                            // Handle languages tab
                                        } else if ($tab_slug == 'calendar-options') {

                                            include 'view-edit-calendar-tab-calendar-options.php';
                                        } else if ($tab_slug == 'notes') {

                                            include 'view-edit-calendar-tab-notes.php';

                                            // Handle dynamic tabs
                                        } else {

                                            /**
                                             * Action to dynamically add content for each tab
                                             *
                                             */
                                            do_action('wpbs_submenu_page_calendar_tab_' . $tab_slug);
                                        }

                                        echo '</div>';
                                    }
                                }

                                ?>

                            </div>

                        </div>

                    </div>

                    <?php

                    /**
                     * Action hook to add extra form fields to the main calendar edit area
                     *
                     * @param WPBS_Calendar $calendar
                     *
                     */
                    do_action('wpbs_view_edit_calendar_main', $calendar);

                    ?>

                </div><!-- / Main Post Body Content -->

            </div>



            <!-- Hidden fields -->
            <input type="hidden" name="calendar_id" value="<?php echo $calendar_id; ?>" />

            <!-- Save button -->
            <input type="submit" class="wpbs-save-calendar wpbs-primary-button" value="<?php echo __('Save Calendar', 'wp-booking-system'); ?>" />

            <div id="wpbs-placeholder-editor">
                <?php wp_editor('', 'wpbs_placeholder_editor', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false)); ?>
            </div>



        </div>

    </form>

</div>

<!-- Add Booking modal -->
<?php include WPBS_PLUGIN_DIR . 'includes/base/admin/booking/views/view-add-booking.php'; ?>