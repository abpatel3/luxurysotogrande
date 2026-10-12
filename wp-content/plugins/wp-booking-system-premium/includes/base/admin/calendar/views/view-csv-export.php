<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$calendar_id = (! empty($_GET['calendar_id']) ? absint($_GET['calendar_id']) : 0);
$calendar    = wpbs_get_calendar($calendar_id);

$legend_items          = wpbs_get_legend_items(array('calendar_id' => $calendar_id));

?>

<div class="wrap wpbs-wrap wpbs-wrap-export-csv">

    <?php wpbs_get_wp_admin_header(); ?>

    <!-- Plugin Sticky Header -->
    <div class="wpbs-plugin-sticky-header">

        <!-- Page Title -->
        <span class="wpbs-page-heading">
            <?php echo __('CSV Export', 'wp-booking-system'); ?>
            <span class="wpbs-heading-tag"><?php printf(__('Calendar ID: %d', 'wp-booking-system'), $calendar_id); ?></span>
        </span>

        <!-- Buttons -->
        <div class="wpbs-header-buttons">
            <a href="<?php echo add_query_arg(array('subpage' => 'edit-calendar')); ?>" class="wpbs-secondary-button"><?php echo __('Back to Calendar', 'wp-booking-system'); ?></a>
        </div>

    </div>

    <!-- WP Notifications -->
    <div class="wpbs-notifications">
        <h1 class="wp-heading-inline"></h1>
    </div>



    <!-- Body -->
    <div class="wpbs-plugin-wrapper">

        <div class="wpbs-box">

            <form method="POST" action="">

                <div class="wpbs-box-header">
                    <h2><?php echo __('Export', 'wp-booking-system'); ?></h2>
                </div>

                <!-- Form Fields -->
                <div class="wpbs-box-content">

                    <div class="wpbs-box-content-inner">

                        <!-- Export Legend Items -->
                        <div class="wpbs-form-field">

                            <label class="wpbs-label" for="csv-export-legend-items"><?php echo __('Legend Items to Export', 'wp-booking-system'); ?></label>

                            <div class="wpbs-input wpbs-chosen-wrapper">

                                <select multiple class="wpbs-chosen" name="csv-export-legend-items[]" id="csv-export-legend-items">
                                    <?php
                                    foreach ($legend_items as $legend_item) {
                                        echo '<option ' . (isset($_POST['csv-export-legend-items']) && in_array($legend_item->get('id'), $_POST['csv-export-legend-items']) ? 'selected' : '') . ' value="' . esc_attr($legend_item->get('id')) . '">' . $legend_item->get('name') . '</option>';
                                    }
                                    ?>
                                </select>

                            </div>

                            <div class="wpbs-label-description"><?php echo __('Leave empty if you wish to export all legend items.', 'wp-booking-system') ?></div>

                        </div>

                        <!-- Export Format -->
                        <div class="wpbs-form-field">

                            <label class="wpbs-label" for="csv-date-format"><?php echo __('Date Format', 'wp-booking-system'); ?></label>

                            <div class="wpbs-input">

                                <select name="csv-date-format" id="csv-date-format">
                                    <option <?php echo (isset($_POST['csv-date-format']) && !empty($_POST['csv-date-format'])) ? selected($_POST['csv-date-format'], 'groupped_date', false) : ''; ?> value="groupped_date"><?php echo __('as a single field', 'wp-booking-system') ?></option>
                                    <option <?php echo (isset($_POST['csv-date-format']) && !empty($_POST['csv-date-format'])) ? selected($_POST['csv-date-format'], 'individual_dates', false) : ''; ?> value="individual_dates"><?php echo __('separate year, month and day fields', 'wp-booking-system') ?></option>
                                </select>

                            </div>

                        </div>

                        <!-- Period -->
                        <div class="wpbs-form-field">

                            <label class="wpbs-label" for="csv-export-period"><?php echo __('Export Period', 'wp-booking-system'); ?></label>

                            <div class="wpbs-input wpbs-has-columns">

                                <div class="wpbs-half">
                                    <input value="<?php echo (isset($_POST['wpbs-export-csv-start-date']) && !empty($_POST['wpbs-export-csv-start-date'])) ? esc_attr($_POST['wpbs-export-csv-start-date']) : ''; ?>" type="text" class="wpbs-export-csv-date" name="wpbs-export-csv-start-date" id="wpbs-export-csv-start-date" placeholder="from" readonly>
                                </div>

                                <div class="wpbs-half">
                                    <input value="<?php echo (isset($_POST['wpbs-export-csv-end-date']) && !empty($_POST['wpbs-export-csv-end-date'])) ? esc_attr($_POST['wpbs-export-csv-end-date']) : ''; ?>" type="text" class="wpbs-export-csv-date" name="wpbs-export-csv-end-date" id="wpbs-export-csv-end-date" placeholder="to" readonly>
                                </div>

                            </div>

                        </div>

                        <!-- iCalendar Events -->
                        <div class="wpbs-form-field">

                            <label class="wpbs-label" for="csv-icalendar-events"><?php echo __('Include iCalendar Events?', 'wp-booking-system'); ?></label>

                            <div class="wpbs-input">

                                <select name="csv-icalendar-events" id="csv-icalendar-events">
                                    <option <?php echo (isset($_POST['csv-icalendar-events']) && !empty($_POST['csv-icalendar-events'])) ? selected($_POST['csv-icalendar-events'], 'no', false) : ''; ?> value="no">No</option>
                                    <option <?php echo (isset($_POST['csv-icalendar-events']) && !empty($_POST['csv-icalendar-events'])) ? selected($_POST['csv-icalendar-events'], 'yes', false) : ''; ?> value="yes">Yes</option>
                                </select>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="wpbs-box-footer-wrapper">
                    <div class="wpbs-box-footer">
                        <input type="submit" class="wpbs-primary-button" value="<?php echo __('Export CSV', 'wp-booking-system'); ?>" />

                        <!-- Calendar ID -->
                        <input type="hidden" name="calendar_id" value="<?php echo $calendar_id; ?>" />

                        <!-- Nonce -->
                        <?php wp_nonce_field('wpbs_csv_export', 'wpbs_token', false); ?>
                        <input type="hidden" name="wpbs_action" value="csv_export" />

                    </div>
                </div>


            </form>

        </div>

    </div>

</div><!-- / Row -->