<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Bookings_Outputter
{

    /**
     * The ID of the calendar
     *
     * @access protected
     * @var    int
     *
     */
    protected $calendar_id;

    /**
     * The bookings
     *
     * @access protected
     * @var    array
     *
     */
    protected $bookings;

    /**
     * The booking tabs
     *
     * @access protected
     * @var    array
     *
     */
    protected $tabs;

    /**
     * The plugin general settings
     *
     * @access protected
     * @var    array
     *
     */
    protected $plugin_settings = array();

    /**
     * 
     * Determine whether to use AJAX for pagination of bookings or load everything with javascript.
     * 
     */
    protected $use_ajax = false;

    /**
     * Query args for the bookings display
     *
     * @access protected
     * @var    array
     *
     */
    protected $args = array();

    /**
     * Hide Past Bookings option
     *
     * @access protected
     * @var    bool
     *
     */
    protected $hide_past_bookings;

    /**
     * Order arg
     *
     * @access protected
     * @var    string
     *
     */
    protected $order;

    /**
     * Orderby arg
     *
     * @access protected
     * @var    string
     *
     */
    protected $orderby;

    /**
     * Current Tab
     *
     * @access protected
     * @var    string
     *
     */
    protected $current_tab;

    /**
     * Total bookings count
     *
     * @access protected
     * @var    object
     *
     */
    protected $total_bookings;

    /**
     * Pagination args
     *
     * @access protected
     * @var    object
     *
     */
    protected $pagination;

    /**
     * Constructor
     *
     * @param $calendar_id
     *
     */
    public function __construct($calendar_id, $args = false)
    {

        /**
         * Set the args
         * 
         */
        $this->args = $args;

        /**
         * Set calendar ID
         *
         */
        $this->calendar_id = absint($calendar_id);

        /**
         * Set the tabs
         *
         */
        $this->tabs = array(
            'pending' => array(
                'name' => __('Pending', 'wp-booking-system'),
                'icon' => 'dashicons-marker',
            ),
            'accepted' => array(
                'name' => __('Accepted', 'wp-booking-system'),
                'icon' => 'dashicons-yes-alt'
            ),
            'trash' => array(
                'name' => __('Deleted', 'wp-booking-system'),
                'icon' => 'dashicons-dismiss'
            )
        );

        /**
         * Set plugin settings
         *
         */
        $this->plugin_settings = get_option('wpbs_settings', array());

        $this->hide_past_bookings = isset($args['hidePastBookings']) ? $args['hidePastBookings'] : (get_user_meta(get_current_user_id(), 'wpbs_remember_hide_past_bookings_option', true) ?: '');
        $this->order = isset($args['order']) ? $args['order'] : (get_user_meta(get_current_user_id(), 'wpbs_remember_order_bookings_option', true) ?: 'desc');
        $this->orderby = isset($args['orderby']) ? $args['orderby'] : (get_user_meta(get_current_user_id(), 'wpbs_remember_orderby_bookings_option', true) ?: 'id');

        $this->current_tab = isset($args['tab']) ? $args['tab'] : 'pending';


        /**
         * Get the bookings
         *
         */
        $query_args = array('calendar_id' => $this->calendar_id);

        global $wpdb;

        // Get total number of bookings
        $total_bookings = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wpbs_bookings WHERE calendar_id='" . $this->calendar_id . "'");

        // Set some defaults
        $this->total_bookings = new stdClass;
        $this->total_bookings->pending = 0;
        $this->total_bookings->accepted = 0;
        $this->total_bookings->pending = 0;
        $this->pagination = new stdClass;
        $this->pagination->page = 0;
        $this->pagination->posts_per_page = 0;
        $this->pagination->total_pages = 0;

        // Check if there are more than 200 bookings and switch to ajax-driven booking listing.
        if ($total_bookings > 200) {
            $this->use_ajax = true;

            $search_string = '';
            if (isset($args['search']) && !empty(trim($args['search']))) {
                $search_string = " AND (id LIKE CONCAT('%','" . esc_sql(strtolower(trim($args['search']))) . "','%') OR LOWER(fields) LIKE CONCAT('%','" . esc_sql(strtolower(trim($args['search']))) . "','%'))";
            }

            $query_args['number'] = apply_filters('wpbs_dashboard_bookings_per_page', 5);

            $this->total_bookings->accepted = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wpbs_bookings WHERE calendar_id='" . $this->calendar_id . "' " . $search_string . " AND status='accepted'" . ($this->hide_past_bookings ? ' AND end_date >= DATE(NOW())' : ''));
            $this->total_bookings->pending = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wpbs_bookings WHERE calendar_id='" . $this->calendar_id . "' " . $search_string . " AND status='pending'"  . ($this->hide_past_bookings ? ' AND end_date >= DATE(NOW())' : ''));
            $this->total_bookings->trash = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wpbs_bookings WHERE calendar_id='" . $this->calendar_id . "' " . $search_string . " AND status='trash'"  . ($this->hide_past_bookings ? ' AND end_date >= DATE(NOW())' : ''));

            $query_args['status'] = [$this->current_tab];
            $query_args['order'] = $this->order;
            $query_args['orderby'] = str_replace(array('check-in-date', 'check-out-date'), array('start_date', 'end_date'), $this->orderby);
            $query_args['custom_query'] = '';

            if ($this->hide_past_bookings) {
                $query_args['custom_query'] .= ' AND end_date >= DATE(NOW())';
            }

            $this->pagination->page = isset($args['page']) ? absint($args['page']) : 1;
            $this->pagination->posts_per_page = $query_args['number'];
            $this->pagination->total_pages = ceil($this->total_bookings->{$this->current_tab} / $this->pagination->posts_per_page);

            $query_args['offset'] = ($this->pagination->page - 1) * $query_args['number'];

            if (!empty($search_string)) {
                $query_args['custom_query'] = $search_string;
            }
        }

        $this->bookings = apply_filters('wpbs_bookings_outputter_bookings', wpbs_get_bookings($query_args), $query_args);
    }

    /**
     * Displays the Bookings meta box content
     *
     */
    public function display()
    {


        $output = '<div id="wpbs-bookings" class="' . ($this->use_ajax ? 'wpbs-bookings-ajax' : 'wpbs-bookings-javascript') . '"
                data-tab="' . $this->current_tab . '" 
                data-calendar-id="' . $this->calendar_id . '" 
                data-hide-past-bookings="' . $this->hide_past_bookings . '"
                data-order="' . $this->order . '"
                data-orderby="' . $this->orderby . '"
                data-search="' . (isset($this->args['search']) ? $this->args['search'] : '') . '"
            >';

        $output .= '<div class="wpbs-box-content">';

        $output .= $this->header();

        $output .= '<div class="wpbs-booking-fields">';

        $output .= $this->bookings();

        $output .= '</div>';

        $output .= '</div>';


        $output .= '<div class="wpbs-box-footer-wrapper"><div class="wpbs-box-footer"><div class="wpbs-box-footer-bookings">';

        $output .= '<div class="wpbs-buttons">';

        $output .= '<button id="wpbs-add-booking-open-modal" class="wpbs-primary-button">' . __('Add Booking', 'wp-booking-system') . '</button>';

        $output .= '<a class="wpbs-bookings-export wpbs-secondary-button" href="' .  wp_nonce_url(add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'edit-calendar', 'wpbs_action' => 'export_bookings', 'calendar_id' => $this->calendar_id), admin_url('admin.php')), 'wpbs_export_bookings', 'wpbs_token') . '"><i class="dashicons dashicons-download"></i> ' . __('Export Bookings as CSV', 'wp-booking-system') . '</a>';

        $output .= '<a href="' . wp_nonce_url(add_query_arg(array('page' => 'wpbs-calendars', 'wpbs_action' => 'permanently_delete_all_bookings',  'calendar_id' => $this->calendar_id), admin_url('admin.php')), 'wpbs_permanently_delete_all_bookings', 'wpbs_token') . '" id="wpbs-empty-trash" class="wpbs-secondary-button wpbs-remove-button">' . __('Remove All Deleted Bookings', 'wp-booking-system') . '</a>';

        $output .= '</div>';

        $output .= $this->pagination();

        $output .= '</div></div></div>';

        $output .= '</div>';

        echo $output;
    }

    /**
     * Returns the header of the booking meta box
     *
     * Tabs, Search and Sort controls
     *
     * @return string
     *
     */
    protected function header()
    {
        $output = '';

        $output .= $this->tabs();

        $output .= '<div class="wpbs-bookings-header">';

        $output .= '<div class="wpbs-form-field">';

        $output .= $this->sorting();
        $output .= $this->search();

        $output .= '</div>';

        $output .= '</div>';

        return $output;
    }

    /**
     * Returns the booking tabs
     *
     * @return string
     *
     */
    protected function tabs()
    {

        $output = '<ul class="wpbs-bookings-tab-navigation subsubsub">';
        foreach ($this->tabs as $tab_id => $tab_data) {
            $active_class = $tab_id == $this->current_tab ? 'class="current"' : '';
            $count = $this->use_ajax ? $this->total_bookings->$tab_id : '';
            $output .= '<li class="' . $tab_id . '"><a href="#" ' . $active_class . ' data-tab="wpbs-bookings-tab-' . $tab_id . '" data-tab-name="' . $tab_id . '"><span class="dashicons ' . $tab_data['icon'] . '"></span> <span class="tab-label">' . $tab_data['name'] . '</span> <span class="count">(' . $count . ')</span></a><span class="separator"> |</span></li>';
        }
        $output .= '</ul>';

        return $output;
    }

    /**
     * Returns the booking sorting dropdowns
     *
     * @return string
     *
     */
    protected function sorting()
    {
        $output = '';

        $output .= '<div class="wpbs-input"><div class="wpbs-input-checkbox hide-past-bookings"><input type="checkbox" value="hide-past-bookings" id="hide-past-bookings" ' . ($this->hide_past_bookings ? 'checked' : '') . ' /><label class="hide-past-bookings-wrapper" for="hide-past-bookings">' . __('Hide past bookings', 'wp-booking-system') . '</label></div></div>';

        $output .= '<div class="wpbs-input"><select id="wpbs-bookings-order-by">';
        $output .= '<option value="" disabled ' . (empty($this->orderby) ? 'selected' : '') . '>' . __('Sort by', 'wp-booking-system') . '</option>';
        $output .= '<option value="id" ' . (!empty($this->orderby) && $this->orderby == 'id' ? 'selected' : '') . '>' . __('Date', 'wp-booking-system') . '</option>';
        $output .= '<option value="check-in-date" ' . (!empty($this->orderby) && $this->orderby == 'check-in-date' ? 'selected' : '') . '>' . __('Check-in date', 'wp-booking-system') . '</option>';
        $output .= '<option value="check-out-date" ' . (!empty($this->orderby) && $this->orderby == 'check-out-date' ? 'selected' : '') . '>' . __('Check-out date', 'wp-booking-system') . '</option>';
        $output .= '</select></div>';

        $output .= '<div class="wpbs-input"><select id="wpbs-bookings-order">';
        $output .= '<option value="" disabled ' . (empty($this->order) ? 'selected' : '') . '>' . __('Sort order', 'wp-booking-system') . '</option>';
        $output .= '<option value="asc"' . (!empty($this->order) && $this->order == 'asc' ? 'selected' : '') . '>' . __('Ascending', 'wp-booking-system') . '</option>';
        $output .= '<option value="desc"' . (!empty($this->order) && $this->order == 'desc' ? 'selected' : '') . '>' . __('Descending', 'wp-booking-system') . '</option>';
        $output .= '</select></div>';

        return $output;
    }

    /**
     * Returns the booking search input
     *
     * @return string
     *
     */
    protected function search()
    {
        $output = '<div class="wpbs-input">';
        $output .= '<input type="search" id="wpbs-bookings-search" name="wpbs-bookings-search" value="' . (isset($this->args['search']) ? $this->args['search'] : '') . '" placeholder="' . __('Search bookings', 'wp-booking-system') . '" >';
        $output .= '</div>';

        return $output;
    }

    /**
     * Returns the bookings grouped by tabs
     *
     * @return string
     *
     */
    protected function bookings()
    {

        $output = '';

        // Loop through tabs
        foreach ($this->tabs as $tab_id => $tab_name) {
            $active_class = $tab_id == $this->current_tab ? 'active' : '';

            $output .= '<div class="wpbs-bookings-tab ' . $active_class . '" id="wpbs-bookings-tab-' . $tab_id . '">';

            // Loop through bookings
            if ($this->bookings) foreach ($this->bookings as $booking) {

                // Skif if not in the correct tab
                if ($booking->get('status') != $tab_id) {
                    continue;
                }

                $output .= $this->booking($booking);
            }

            $output .= '</div>';
        }

        $output .= '<p class="wpbs-bookings-no-results">' . sprintf(__("You don't have any %s bookings.", 'wp-booking-system'), '<strong></strong>') . '</p>';
        $output .= '<p class="wpbs-bookings-no-search-results">' . sprintf(__("No results for %s.", 'wp-booking-system'), '<strong></strong>') . '</p>';

        return $output;
    }

    /**
     * Returns the bookings
     *
     * @return string
     *
     */
    protected function booking($booking)
    {
        $output = '';
        $output .= '<div class="wpbs-booking-field wpbs-open-booking-details wpbs-booking-field-is-read-' . ($booking->get('is_read') ? 1 : 0) . ' ' . (date('Ymd',strtotime($booking->get('end_date'))) < wp_date('Ymd') ? 'wpbs-is-past-booking' . ($this->hide_past_bookings ? ' wpbs-hide-past-booking' : '') : '') . '"
                    data-id="' . $booking->get('id') . '"
                    data-check-in-date="' . strtotime($booking->get('start_date')) . '"
                    data-check-out-date="' . strtotime($booking->get('end_date')) . '">';

        $output .= '<div class="wpbs-booking-field-inner">';
        $output .= '<div class="wpbs-booking-field-header">';
        $output .= '<div class="wpbs-booking-field-header-fixed-elements">';
        $output .= '<div class="wpbs-booking-field-booking-id ' . ($booking->get('status') == 'pending' ? 'wpbs-booking-color-status-pending' : '') . ' wpbs-booking-color-' . ($booking->get('id') % 10) . '">#' . $booking->get('id') . '</div>';
        $output .= '<p class="wpbs-booking-field-check-in-date">';
        $output .= '<i class="wpbs-icon-check-in"></i>';
        $output .= '<span class="wpbs-booking-field-header-label"> ' . wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('start_date'))) . '</span>';
        $output .= '</p>';
        $output .= '<p class="wpbs-booking-field-check-out-date">';
        $output .= '<i class="wpbs-icon-check-out"></i>';
        $output .= '<span class="wpbs-booking-field-header-label"> ' . wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('end_date'))) . '</span>';
        $output .= '</p>';
        $output .= '</div>';
        $output .= $this->booking_details($booking);

        $output .= '<div class="wpbs-booking-field-tags">';
        $output .= apply_filters('wpbs_booking_outputter_booking_tags', '', $booking);
        $output .= $this->manual_booking($booking);
        $output .= $this->refund_status($booking);
        $output .= $this->payment_status($booking);
        $output .= $this->is_read($booking);
        $output .= '</div>';

        $output .= '</div>';
        $output .= '</div>';

        $output .= '</div>';

        return $output;
    }

    /**
     * Returns the booking detail field
     *
     * @param WPBS_Booking $booking
     *
     * @return string
     *
     */
    protected function booking_details($booking)
    {
        $details = apply_filters('wpbs_booking_outputter_booking_details', '', $booking);
        $i = 1;
        foreach ($booking->get('fields') as $field) {
            if (!isset($field['user_value']) || empty($field['user_value'])) {
                continue;
            }

            // Exclude some fields
            if (in_array($field['type'], wpbs_get_excluded_fields())) {
                continue;
            }

            if (in_array($field['id'], apply_filters('wpbs_booking_outputter_preview_skip_fields', [], $this->calendar_id, $booking))) {
                continue;
            }

            // Handle Pricing options differently
            if (wpbs_form_field_is_product($field['type'])) {
                $field['user_value'] = wpbs_get_form_field_product_values($field);
            }

            $user_value = wpbs_get_field_display_user_value($field['user_value']);

            if ($field['type'] == 'payment_method') {
                $user_value = isset(wpbs_get_payment_methods()[$user_value]) ? wpbs_get_payment_methods()[$user_value] : '';
            }

            // Add all fields, but keep some hidden for serach purposes.
            if ($i++ <= 8) {
                $details .= '<span><strong>' . $this->get_translated_label($field) . ':</strong> <span>' . $user_value . '</span></span>';
            } else {
                $details .= '<span class="wpbs-hidden">' . $user_value . '</span>';
            }
        }

        // Add Transaction ID for search purposes
        $payment = wpbs_get_payment_by_booking_id($booking->get('id'));
        if ($payment) {
            $details .= '<span class="wpbs-hidden">' . $payment->get('order_id') . '</span>';
        }

        $output = '<p class="wpbs-booking-field-details">' . $details . '</p>';
        return $output;
    }

    /**
     * Helper function to get label translations
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_translated_label($field)
    {
        $language = wpbs_get_locale();
        if (isset($field['values'][$language]['label']) && !empty($field['values'][$language]['label'])) {
            return $field['values'][$language]['label'];
        }

        if (isset($field['values']['default']['label'])) {
            return $field['values']['default']['label'];
        }

        return '(no label)';
    }

    /**
     * Generates and returns the HTML for the "new" label for unread bookings
     *
     * @param WPBS_Booking $booking
     *
     * @return string
     *
     */
    protected function is_read($booking)
    {
        if ($booking->get('is_read')) {
            return false;
        }

        return '<div class="wpbs-booking-field-new-booking"></div>';
    }

    /**
     * Show if the booking is a manual booking or not
     *
     * @param WPBS_Booking $booking
     *
     */
    public static function manual_booking($booking)
    {

        if (wpbs_get_booking_meta($booking->get('id'), 'manual_booking', 'true')) {
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-manual-booking">' . __('Manual Booking', 'wp-booking-system') . '</div>';
        }
    }


    /**
     * Add a payment status tag to the bookings
     *
     * @param WPBS_Booking $booking
     *
     */
    public static function payment_status($booking)
    {

        // Check if pricing is enabled
        if (!wpbs_is_pricing_enabled()) {
            return false;
        }

        // Get payments for current booking
        $payment = wpbs_get_payment_by_booking_id($booking->get('id'));

        if (!$payment) {
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-no-payment">' . __('No Payment', 'wp-booking-system') . '</div>';
        }

        if (empty($payment)) {
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-no-payment">' . __('No Payment', 'wp-booking-system') . '</div>';
        }

        // Handle Payment on Arrival
        if ($payment->get('gateway') == 'payment_on_arrival') {
            if ($payment->is_paid()) {
                return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-paid">' . __('Paid', 'wp-booking-system') . '</div>';
            }
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-payment-on-arrival">' . __('Payment on Arrival', 'wp-booking-system') . '</div>';
        }

        // Error
        if ($payment->get('order_status') == 'error') {
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-not-paid">' . __('Error', 'wp-booking-system') . '</div>';
        }
        // Cancelled
        if ($payment->get('order_status') == 'cancelled') {
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-not-paid">' . __('Cancelled', 'wp-booking-system') . '</div>';
        }

        // Delayed Capture
        if ($payment->get('order_status') == 'authorized' || $payment->get('order_status') == 'requires_capture') {
            if (strtotime($payment->get('date_created')) < current_time('timestamp') - (DAY_IN_SECONDS * 7)) {
                return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-not-paid">' . __('Error', 'wp-booking-system') . '</div>';
            }
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-authorized">' . __('Authorized', 'wp-booking-system') . '</div>';
        }

        // Delayed Capture
        if ($payment->get('order_status') == 'pending') {
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-deposit-paid">' . __('Pending', 'wp-booking-system') . '</div>';
        }

        // Handle "Not Paid" bookings
        if (
            ($payment->is_part_payment() && !$payment->is_deposit_paid()) || // Part payments enabled but deposit wasn't paid
            ($payment->get('gateway') == 'bank_transfer' && !$payment->is_part_payment() && !$payment->is_paid()) || // Bank transfer not paid
            (wpbs_get_booking_meta($payment->get('booking_id'), 'manual_booking', true) && !$payment->is_part_payment() && !$payment->is_paid()) // Manual bookings non part payment not paid
        ) {
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-not-paid">' . __('Not Paid', 'wp-booking-system') . '</div>';
        }

        // Handle Deposits
        if ($payment->is_part_payment() && $payment->is_deposit_paid() && !$payment->is_final_payment_paid()) { // Only Deposit was paid
            if (wpbs_get_booking_meta($booking->get('id'), 'manual_booking', 'true') && !apply_filters('wpbs_payment_method_supports_manual_deposit_' . $payment->get_payment_gateway(), false)) {
                return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-not-paid">' . __('Not Paid', 'wp-booking-system') . '</div>';
            }
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-deposit-paid">' . __('Deposit Paid', 'wp-booking-system') . '</div>';
        }

        // If we got so far, it means the booking was paid for.
        return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-paid">' . __('Paid', 'wp-booking-system') . '</div>';
    }

    /**
     * Add a payment status tag to the bookings
     *
     * @param WPBS_Booking $booking
     *
     */
    public static function security_deposit_status($booking)
    {
        // Get payments for current booking
        $payment = wpbs_get_payment_by_booking_id($booking->get('id'));

        if (!$payment) {
            return '<div class="wpbs-booking-field-security-deposit-status-tag wpbs-booking-field-security-deposit-status-tag-na"><abbr title="' . __('No payment was received for this booking.', 'wp-booking-system') . '">' . __('N/A', 'wp-booking-system') . '</abbr></div>';
        }

        $details = $payment->get('details');

        $status = wpbs_get_security_deposit_status($payment);

        if ($status == 'refunded') {
            $abbr = sprintf(__('Refunded on %s.', 'wp-booking-system'),  wpbs_date_i18n(get_option('date_format'), $details['security_deposit']['action_date']));
            return '<div class="wpbs-booking-field-security-deposit-status-tag wpbs-booking-field-security-deposit-status-tag-ok"><abbr title="' . $abbr . '">' . __('Refunded', 'wp-booking-system') . '</abbr></div>';
        }

        if ($status == 'cancelled') {
            $abbr = sprintf(__('Refund cancelled on %s.', 'wp-booking-system'),  wpbs_date_i18n(get_option('date_format'), $details['security_deposit']['action_date']));
            return '<div class="wpbs-booking-field-security-deposit-status-tag wpbs-booking-field-security-deposit-status-tag-ok"><abbr title="' . $abbr . '">' . __('Cancelled', 'wp-booking-system') . '</abbr></div>';
        }

        if ($status == 'not_supported') {
            return '<div class="wpbs-booking-field-security-deposit-status-tag wpbs-booking-field-security-deposit-status-tag-na"><abbr title="' . __('The payment method used does not support automatic refunds.', 'wp-booking-system') . '">' . __('N/A', 'wp-booking-system') . '</abbr></div>';
        }

        if ($status == 'awaiting_final_payment') {
            return '<div class="wpbs-booking-field-security-deposit-status-tag wpbs-booking-field-security-deposit-status-tag-pending"><abbr title="' . __('Awaiting final payment.', 'wp-booking-system') . '">' . __('Pending', 'wp-booking-system') . '</abbr></div>';
        }

        if ($status == 'error') {
            $abbr = __('The automatic attempt to issue the refund has failed. Reason: ', 'wp-booking-system') . (isset($details['security_deposit']['error']) ? $details['security_deposit']['error'] : '-');
            return '<div class="wpbs-booking-field-security-deposit-status-tag wpbs-booking-field-security-deposit-status-tag-not-ok"><abbr title="' . $abbr . '">' . __('Error', 'wp-booking-system') . '</abbr></div>';
        }

        if ($status == 'scheduled') {

            $crons = _get_cron_array();

            foreach ($crons as $timestamp => $cron) {
                if (isset($cron['wpbs_refund_security_deposit_cron'])) {
                    foreach ($cron['wpbs_refund_security_deposit_cron'] as $job) {
                        if ($job['args'][0] == $payment->get('booking_id')) {
                            $abbr = sprintf(__('Scheduled to be refunded on %s.', 'wp-booking-system'), wpbs_date_i18n(get_option('date_format'), $timestamp));
                            return '<div class="wpbs-booking-field-security-deposit-status-tag wpbs-booking-field-security-deposit-status-tag-pending"><abbr title="' . $abbr . '">' . __('Pending', 'wp-booking-system') . '</abbr></div>';
                        }
                    }
                }
            }

            return '<div class="wpbs-booking-field-security-deposit-status-tag wpbs-booking-field-security-deposit-status-tag-not-ok"><abbr title="' . __('Not scheduled. Can only be refunded manually.', 'wp-booking-system') . '">' . __('Error', 'wp-booking-system') . '</abbr></div>';
        }

        return '<div class="wpbs-booking-field-security-deposit-status-tag wpbs-booking-field-security-deposit-status-tag-na"><abbr title="' . __('No status available.', 'wp-booking-system') . '">' . __('N/A', 'wp-booking-system') . '</abbr></div>';
    }

    /**
     * Add a refund status tag to the bookings
     *
     * @param WPBS_Booking $booking
     *
     */
    public static function refund_status($booking)
    {
        // Check if pricing is enabled
        if (!wpbs_is_pricing_enabled()) {
            return false;
        }

        // Get payments for current booking
        $payment = wpbs_get_payment_by_booking_id($booking->get('id'));

        if (!$payment) {
            return '';
        }

        if ($payment->get_refund_status() == 'not_refunded') {
            return '';
        }

        if ($payment->get_refund_status() == 'partially_refunded') {
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-partially-refunded">' . __('Partially Refunded', 'wp-booking-system') . '</div>';
        }

        if ($payment->get_refund_status() == 'fully_refunded') {
            return '<div class="wpbs-booking-field-payment-status-tag wpbs-booking-field-payment-status-tag-fully-refunded">' . __('Fully Refunded', 'wp-booking-system') . '</div>';
        }
    }

    /**
     * Returns the booking pagination
     *
     * @return string
     *
     */
    protected function pagination()
    {

        if ($this->use_ajax && $this->pagination->total_pages == 1) {
            return '';
        }

        $output = '';
        $output .= '<div class="tablenav-pages wpbs-bookings-pagination">';
        $output .= '<span class="displaying-num"><span>' . $this->total_bookings->{$this->current_tab} . '</span> ' . __('bookings', 'wp-booking-system') . '</span>';
        $output .= '<span class="pagination-links">';
        $output .= '<a class="first-page wpbs-secondary-button ' . ($this->pagination->page == 1 ? 'disabled' : '') . '" data-page="1" href="#"><span aria-hidden="true">&laquo;</span></a>';
        $output .= '<a class="prev-page wpbs-secondary-button ' . ($this->pagination->page == 1 ? 'disabled' : '') . '" data-page="' . ($this->pagination->page == 1 ? 1 : ($this->pagination->page - 1)) . '" href="#"><span aria-hidden="true">&lsaquo;</span></a>';
        $output .= '<span class="paging-input">';
        $output .= '<span class="tablenav-paging-text"><span class="current-page">' . $this->pagination->page . '</span> ' . __('of', 'wp-booking-system') . ' <span class="total-pages">' . $this->pagination->total_pages . '</span></span>';
        $output .= '</span>';

        $output .= '<a class="next-page wpbs-secondary-button ' . ($this->pagination->page == $this->pagination->total_pages ? 'disabled' : '') . '" data-page="' . ($this->pagination->page == $this->pagination->total_pages ? $this->pagination->total_pages : ($this->pagination->page + 1)) . '" href="#"><span aria-hidden="true">&rsaquo;</span></a>';
        $output .= '<a class="last-page wpbs-secondary-button ' . ($this->pagination->page == $this->pagination->total_pages ? 'disabled' : '') . '" data-page="' . $this->pagination->total_pages . '" href="#"><span aria-hidden="true">&raquo;</span></a>';
        $output .= '</span>';
        $output .= '</div>';

        return $output;
    }
}
