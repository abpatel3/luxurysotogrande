<?php
$settings = get_option('wpbs_settings', array());
$active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());
$active_section = (isset($_GET['section']) && ! empty($_GET['section'] && array_key_exists($_GET['section'], $payment_tabs)) ? sanitize_text_field($_GET['section']) : 'payment_on_arrival');
$languages = wpbs_get_languages();
?>
<ul class="wpbs-sub-tab-navigation">
    <?php
    if (! empty($payment_tabs)) {
        $i = 0;
        foreach ($payment_tabs as $tab_slug => $tab_name) {

            echo '<li><a href="' . add_query_arg(array('page' => 'wpbs-settings', 'tab' => 'payment_gateways', 'section' => $tab_slug), admin_url('admin.php')) . '" data-tab="' . $tab_slug . '" ' . ($active_section == $tab_slug  ? ' class="current"' : '') . '>' . $tab_name . '</a></li>';
            $i++;
        }
    }
    ?>
</ul>

<div class="wpbs-clear"><!-- --></div>

<div class="wpbs-sub-tabs">

    <?php

    if (! empty($payment_tabs)) {

        foreach ($payment_tabs as $tab_slug => $tab_name) {

            echo '<div class="wpbs-tab wpbs-tab-' . $tab_slug . ($active_section == $tab_slug ? ' wpbs-section-active' : '') . ' " data-tab="' . $tab_slug . '">';

            // Handle Payment on Arrival tab
            if ($tab_slug == 'payment_on_arrival') {

                include 'view-payment-settings-payment-on-arrival.php';

                // Handle Payment on Arrival tab
            } else if ($tab_slug == 'bank_transfer') {

                include 'view-payment-settings-bank-transfer.php';

                // Handle String Translations tab
            } else {

                /**
                 * Action to dynamically add content for each tab
                 *
                 */
                do_action('wpbs_submenu_page_payment_gateways_tab_' . $tab_slug);
            }
            echo '</div>';
        }
    }

    ?>

</div>

<!-- Save Page Button -->
<div class="wpbs-box-footer-wrapper">
    <div class="wpbs-box-footer">
        <input type="submit" class="wpbs-primary-button" value="<?php echo __('Save Settings', 'wp-booking-system'); ?>" />
    </div>
</div>