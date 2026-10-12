<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add the Payment Options tab to the Payment Settings page
 *
 */
function wpbs_submenu_page_edit_form_sub_tabs_payment_options($tabs)
{

    if (!wpbs_is_pricing_enabled()) {
        return $tabs;
    }

    $tabs['form-options']['payment_options'] = __('Payment Options', 'wp-booking-system');

    return $tabs;
}
add_filter('wpbs_submenu_page_edit_form_sub_tabs', 'wpbs_submenu_page_edit_form_sub_tabs_payment_options', 1, 1);

/**
 * Add the Payment Options tab content  to the Payment Settings page
 *
 */
function wpbs_submenu_page_edit_form_tabs_form_options_payment_options()
{
    include 'views/view-edit-form-tab-payment-options.php';

}
add_action('wpbs_submenu_page_edit_form_tabs_form_options_payment_options', 'wpbs_submenu_page_edit_form_tabs_form_options_payment_options', 100, 1);