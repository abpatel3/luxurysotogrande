<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;


/**
 * Base class for creating payment methods
 *
 */
abstract class WPBS_Payment_Method
{

    public $slug;
    public $name;
    public $display_name;
    public $description;
    public $settings;

    /**
     * Constructor
     *
     */
    public function __construct()
    {

        $this->init();

        $this->settings = get_option('wpbs_settings', array());

        // Register Payment Method
        add_filter('wpbs_payment_methods', array($this, 'register_payment_method'));

        // Get the payment methods name
        add_filter('wpbs_form_outputter_payment_method_name_' . $this->slug, array($this, 'form_outputter_payment_method_name'), 10, 2);

        // Get the payment methods description
        add_filter('wpbs_form_outputter_payment_method_description_' . $this->slug, array($this, 'form_outputter_payment_method_description'), 10, 2);

        // Settings Page
        add_filter('wpbs_submenu_page_settings_payment_gateway_tabs', array($this, 'settings_page_tab'), 20);
        add_filter('wpbs_submenu_page_payment_gateways_tab_' . $this->slug, array($this, 'settings_page_content_wrap'), 20);

        // Ignore reCaptcha on confirmation screen
        add_filter('wpbs_validate_recaptcha_payment_confirmation', array($this, 'validate_recaptcha'), 10, 2);

        // Form Payment Screen
        add_filter('wpbs_submit_form_before', array($this, 'payment_screen_wrap'), 10, 6);

        // Form Secondary Payment Screen
        add_filter('wpbs_final_payment_' . $this->slug, array($this, 'secondary_payment_screen_wrap'), 10, 3);

        // Wehbook
        add_action('init', array($this, 'webhook_wrap'), 10, 1);

        // Save payment when booking is created
        add_action('wpbs_submit_form_after', array($this, 'save_payment'), 10, 5);

        // Display the 'please wait' message in the form after submitting the payment.
        add_action('init', array($this, 'payment_confirmation_placeholder_message'), 20, 1);
        add_action('wp_footer', array($this, 'payment_confirmation_scripts'));

        // AJAX to check the payment status while on the confirmation page.
        add_action('wp_ajax_payment_confirmation_get_payment_status', array($this, 'payment_confirmation_get_payment_status_wrap'), 20);
        add_action('wp_ajax_nopriv_payment_confirmation_get_payment_status', array($this, 'payment_confirmation_get_payment_status_wrap'), 20);

        // Add to the list of enabled payment methods
        add_filter('wpbs_form_outputter_payment_method_enabled_' . $this->slug, array($this, 'is_enabled'));

        // Send payment error emails
        add_action('wpbs_payment_failed_action', array($this, 'payment_failed_action'), 10, 1);

        // Maybe create payment on "Thank You" page
        add_action('init', array($this, 'thank_you_page_actions'));
    }

    /**
     * Helper init method to avoid rewriting of the __construct method by subclasses
     *
     */
    abstract public function init();

    /**
     * The payment screen in the booking form for the initial payment
     * 
     * @param array $data
     * 
     * @return array ['error' => bool, 'output' => string]
     *
     */
    abstract public function payment_screen($data);

    public function payment_screen_wrap($response, $post_data, $form, $form_args, $form_fields, $calendar_id)
    {
        // Check if another payment method was already found
        if ($response !== false) {
            return $response;
        }

        $payment_found = false;

        // Check if payment method is enabled.
        foreach ($form_fields as $form_field) {
            if ($form_field['type'] == 'payment_method' && $form_field['user_value'] == $this->slug) {
                $payment_found = true;
                break;
            }
        }

        if ($payment_found === false) {
            return false;
        }

        // Parse POST data
        parse_str($post_data['form_data'], $form_data);

        // Check if the payment screen was shown
        if (isset($form_data['wpbs-' . $this->slug . '-confirmation-loaded']) && $form_data['wpbs-' . $this->slug . '-confirmation-loaded'] == '1') {
            return false;
        }

        // Add a field to the input so we can check if the payment screen was already shown
        add_filter('wpbs_form_outputter_form_fields_after', function () {
            return '<input type="hidden" name="wpbs-' . $this->slug . '-confirmation-loaded" value="1">';
        });

        // Generate form
        $form_outputter = new WPBS_Form_Outputter($form, $form_args, $form_fields, $calendar_id);

        // Check if post data exists and matches form values
        if (wpbs_validate_payment_form_consistency($form_fields) === false) {
            return json_encode(
                array(
                    'success' => false,
                    'html' => '<p class="wpbs-form-general-error">' . __('Something went wrong. Please refresh the page and try again.', 'wp-booking-system') . '</p>',
                )
            );
        }

        /**
         * Get total price
         * 
         */
        $payment = new WPBS_Payment;
        $payment->calculate_prices($post_data, $form, $form_args, $form_fields);

        $total = $payment->get_total();

        // Check if part payments are used
        if (wpbs_part_payments_enabled() == true && $payment->is_part_payment()) {
            $total = $payment->get_total_first_payment();
        }

        // Check if price is greater than the minimum allowed, 0.5;
        if ($total <= 0.5) {
            return json_encode(
                array(
                    'success' => false,
                    'html' => '<p class="wpbs-form-general-error">' . __("The minimum payable amount is 0.50$", 'wp-booking-system') . '</p>'
                )
            );
        }

        $calendar = wpbs_get_calendar($calendar_id);


        /**
         * Get Invoice item description
         * 
         */
        $invoice_item_description = (!empty($this->settings['payment' . $this->slug . 'invoice_name_translation_' . $form_outputter->get_language()])) ? $this->settings['payment' . $this->slug . 'invoice_name_translation_' . $form_outputter->get_language()] : (!empty($this->settings['payment' . $this->slug . 'invoice_name']) ? $this->settings['payment' . $this->slug . 'invoice_name'] : get_bloginfo('name') . ' Booking');
        $invoice_item_description .= ' - ' . $calendar->get_name();
        $invoice_item_description .= ' - ' . wpbs_date_i18n(get_option('date_format'), wpbs_convert_js_to_php_timestamp($post_data['calendar']['start_date'])) . '-' . wpbs_date_i18n(get_option('date_format'), wpbs_convert_js_to_php_timestamp($post_data['calendar']['end_date']));
        $invoice_item_description = apply_filters('wpbs' . $this->slug . 'invoice_item_description', $invoice_item_description, $calendar, $payment);

        /**
         * Save the booking data in a transient so we can create the booking in the webhook
         * 
         */
        $booking_data = array(
            'post_data' => $post_data,
            'form_id' => $form->get('id'),
            'form_args' => $form_args,
            'form_fields' => $form_fields,
            'calendar_id' => $calendar_id,
        );

        // Generate an unique ID for the transient id
        $booking_data_transient_id = 'wpbs-' . $this->slug . '-' . current_time('timestamp') . '_' . $form_outputter->get_unique();

        // Add some metadata to the Payment Intent
        $metadata = [
            'form_id' => $form->get('id'),
            'booking_data_transient_id' => $booking_data_transient_id
        ];

        // Save all the booking details for later use, if the payment is accepted
        add_option($booking_data_transient_id, $booking_data);

        /**
         * Set Return URL
         * 
         */
        $return_url = add_query_arg([
            'wpbs-' . $this->slug . '-confirmation' => true,
            'wpbs-start-month'  => $post_data['calendar']['current_month'],
            'wpbs-start-year' => $post_data['calendar']['current_year'],
            'wpbs-transient-id' => $booking_data_transient_id,
            'wpbs-language' => $form_outputter->get_language(),
            'form_id' => $form->get('id'),
            'calendar_id' => $calendar_id,
        ], $form_data['wpbs-return-url']);


        /**
         * Get the payment output screen
         * 
         */
        $data = [
            'total' => $total,
            'form' => $form,
            'form_fields' => $form_fields,
            'form_outputter' => $form_outputter,
            'payment' => $payment,
            'invoice_item_description' => $invoice_item_description,
            'post_id' => $form_data['wpbs-post-id'],
            'return_url' => $return_url,
            'booking_page_url' => $form_data['wpbs-return-url'],
            'booking_data_transient_id' => $booking_data_transient_id,
            'language' => $form_outputter->get_language(),
            'metadata' => $metadata,
        ];
        $output = $this->payment_screen($data);

        /**
         * Exit the function if we need to
         * 
         */
        if (isset($output['error']) && $output['error']) {
            return json_encode(
                array(
                    'success' => false,
                    'html' => '<p class="wpbs-form-general-error">' . $output['output']  . '</p>',
                )
            );
        }

        /**
         * Add in other data to the output, like the pricing table and the edit order button
         * 
         */
        $output = wpbs_form_payment_confirmation_screen($form_outputter, $payment, $this->slug, $output['output']);

        /**
         * Send it
         * 
         */
        return json_encode(
            array(
                'success' => false,
                'html' => $output,
            )
        );
    }

    /**
     * The payment screen in the booking form for the initial payment
     * 
     * @param array $data
     * 
     * @return array ['error' => bool, 'output' => string]
     *
     */
    public function secondary_payment_screen_wrap($response, $payment, $language)
    {

        if ($payment->is_part_payment()) {
            if ($payment->is_deposit_paid()) {
                $is_secondary_payment = true;
                $total = $payment->get_total_second_payment();
            } else {
                $is_secondary_payment = false;
                $total = $payment->get_total_first_payment();
            }
        } else {
            $is_secondary_payment = false;
            $total = $payment->get_total();
        }


        $booking = wpbs_get_booking($payment->get('booking_id'));

        if (!$booking) {
            return '<p class="wpbs-form-general-error">' . __("Booking doesn't exist", 'wp-booking-system') . '</p>';
        }

        // Check if price is greater than the minimum allowed, 0.5;
        if ($total <= 0.5) {
            return '<p class="wpbs-form-general-error">' . __("The minimum payable amount is 0.50$", 'wp-booking-system') . '</p>';
        }

        $calendar = wpbs_get_calendar($booking->get('calendar_id'));


        /**
         * Get Invoice item description
         * 
         */
        $invoice_item_description = (!empty($this->settings['payment' . $this->slug . 'invoice_name_translation_' . $language])) ? $this->settings['payment' . $this->slug . 'invoice_name_translation_' . $language] : (!empty($this->settings['payment' . $this->slug . 'invoice_name']) ? $this->settings['payment' . $this->slug . 'invoice_name'] : get_bloginfo('name') . ' Booking');
        $invoice_item_description .= ' - #' . $booking->get('id');
        $invoice_item_description .= ' - ' . $calendar->get_name();
        $invoice_item_description .= ' - ' . wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('start_date'))) . '-' . wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('end_date')));
        $invoice_item_description = apply_filters('wpbs' . $this->slug . 'invoice_item_description', $invoice_item_description, $calendar, $payment);

        /**
         * Save the booking data in a transient so we can create the booking in the webhook
         * 
         */
        $booking_data = array(
            'is_secondary_payment' => $is_secondary_payment,
            'booking_id' => $booking->get('id'),
            'form_id' => $booking->get('form_id'),
            'payment_id' => $payment->get('id'),
            'language' => $language
        );

        // Generate an unique ID for the transient id
        $booking_data_transient_id = 'wpbs-' . $this->slug . '-' . current_time('timestamp') . '_' . $booking->get('id');

        // Add some metadata to the Payment Intent
        $metadata = [
            'form_id' => $booking->get('form_id'),
            'is_secondary_payment' => $is_secondary_payment,
            'booking_id' => $booking->get('id'),
            'payment_id' => $payment->get('id'),
            'booking_data_transient_id' => $booking_data_transient_id
        ];

        // Save all the booking details for later use, if the payment is accepted
        add_option($booking_data_transient_id, $booking_data);

        /**
         * Set Return URL
         * 
         */
        $return_url = add_query_arg([
            'wpbs-' . $this->slug . '-confirmation' => true,
            'wpbs-payment-id' => $payment->get('order_id'),
            'wpbs-transient-id' => $booking_data_transient_id,
            'wpbs-language' => $language,
        ], wpbs_get_current_permalink());


        /**
         * Get the payment output screen
         * 
         */
        $data = [
            'total' => $total,
            'form' => wpbs_get_form($booking->get('form_id')),
            'form_fields' => $booking->get('fields'),
            'payment' => $payment,
            'invoice_item_description' => $invoice_item_description,
            'return_url' => $return_url,
            'language' => $language,
            'booking_data_transient_id' => $booking_data_transient_id,
            'booking_page_url' => add_query_arg(['wpbs-payment-id' => $payment->get('order_id')], wpbs_get_current_permalink()),
            'metadata' => $metadata,
        ];

        $output = $this->payment_screen($data);

        /**
         * Exit the function if we need to
         * 
         */
        if (isset($output['error']) && $output['error']) {
            return '<p class="wpbs-form-general-error">' . $output['output']  . '</p>';
        }

        /**
         * Send it
         * 
         */
        return $output['output'];
    }

    /**
     * Save payment when booking is created
     * 
     */
    abstract public function save_payment($booking_id, $post_data, $form, $form_args, $form_fields);

    /**
     * Wehbook Endpoint
     * 
     */

    abstract public function webhook();

    public function webhook_wrap()
    {
        if (!isset($_GET['wpbs-' . $this->slug . '-webhook'])) {
            return false;
        }

        $this->webhook();
    }


    /**
     * Display the 'please wait' message in the form after submitting the payment.
     * 
     */
    public function payment_confirmation_placeholder_message()
    {

        // Check if we're on the confirmation page
        if (!isset($_GET['wpbs-' . $this->slug . '-confirmation'])) {
            return false;
        }

        $language = esc_attr($_GET['wpbs-language']);

        add_filter('wpbs_form_outputter_form', function ($output, $form, $args, $calendar_id) use ($language) {
            if ($_GET['calendar_id'] == $calendar_id && $_GET['form_id'] == $form->get('id')) {
                return '<div class="wpbs-' . $this->slug . '-payment-confirmation wpbs-payment-confirmation-message-loader"><div class="wpbs-payment-loader"></div><p>' . wpbs_get_payment_default_string('processing_payment', $language) . '</p></div>';
            }
            return $output;
        }, 10, 4);

        add_filter('wpbs_final_payment_output', function ($output) use ($language) {
            return '<div class="wpbs-' . $this->slug . '-payment-confirmation wpbs-payment-confirmation-message-loader"><div class="wpbs-payment-loader"></div><p>' . wpbs_get_payment_default_string('processing_payment', $language) . '</p></div>';
        }, 10, 1);
    }

    /**
     * Wait for payment confirmation and update the form message
     * 
     */
    public function payment_confirmation_scripts()
    {
        // Check if we're on the confirmation page
        if (!isset($_GET['wpbs-' . $this->slug . '-confirmation'])) {
            return false;
        }

?>
        <script>
            document.dispatchEvent(new Event('wpbs_payment_confirmation_page'));
            document.dispatchEvent(new Event('wpbs_<?php echo $this->slug; ?>_payment_confirmation_page'));

            window.addEventListener("load", (event) => {

                if (jQuery('.wpbs-container').length) {
                    jQuery('.wpbs-container').removeClass('wpbs-enable-hover').addClass('wpbs-disable-selection');
                }

                // Scroll to form
                if (jQuery('.wpbs-<?php echo $this->slug; ?>-payment-confirmation').length && wpbs_ajax.scroll_animation_offset != false) {
                    jQuery('html, body').stop().animate({
                        scrollTop: jQuery('.wpbs-<?php echo $this->slug; ?>-payment-confirmation').offset().top - wpbs_ajax.scroll_animation_offset
                    });
                }

                function wpbs_payment_confirmation_get_payment_status() {

                    // Prepare data
                    data = {
                        action: 'payment_confirmation_get_payment_status',
                        data: '<?php echo json_encode($_GET); ?>',
                    }

                    // Make the request
                    jQuery.post(wpbs_ajax.ajax_url, data, function(response) {

                        if (response == 'refresh') {
                            setTimeout(wpbs_payment_confirmation_get_payment_status, 1000);
                            return false;
                        }

                        response = JSON.parse(response);

                        if (response.type == 'message') {
                            if (jQuery('.wpbs-main-wrapper-calendar-' + response.calendar_id + '.wpbs-main-wrapper-form-' + response.form_id + ' .wpbs-<?php echo $this->slug; ?>-payment-confirmation').length) {
                                jQuery('.wpbs-main-wrapper-calendar-' + response.calendar_id + '.wpbs-main-wrapper-form-' + response.form_id + ' .wpbs-<?php echo $this->slug; ?>-payment-confirmation').each(function() {
                                    jQuery(this).removeClass('wpbs-payment-confirmation-message-loader').html(response.message);
                                });
                            } else {
                                jQuery('.wpbs-<?php echo $this->slug; ?>-payment-confirmation').each(function() {
                                    jQuery(this).removeClass('wpbs-payment-confirmation-message-loader').html(response.message);
                                });
                            }

                            history.pushState({}, document.title, window.location.origin + window.location.pathname);
                        }

                        if (response.type == 'redirect') {
                            window.location.href = response.redirect_url;
                        }

                        if (response.type == 'already_displayed') {
                            history.pushState({}, document.title, window.location.origin + window.location.pathname);
                            window.location.reload();
                        }

                    });

                }

                wpbs_payment_confirmation_get_payment_status();

            });
        </script>
<?php
    }

    /**
     * Create the booking from the transient data and check for payment intent ID status.
     * 
     */
    public function create_booking($transient_id, $payment_intent_id = false)
    {

        $lock_name = 'wpbs_booking_lock_' . $transient_id;
        if (false === add_option($lock_name, time(), '', 'no')) {
            return false;
        }

        try {
            $booking_data = get_option($transient_id);
            if (! $booking_data || ! empty($booking_data['is_secondary_payment'])) {
                return false;
            }

            /**
             * If a booking_id exists, it means we are processing the initial payment.
             * 
             */
            if (isset($booking_data['booking_id'])) {

                $confirmation = [
                    'type' => 'message',
                    'message' => isset($this->settings['payment_part_payments_confirmation_translation_' . $booking_data['language']]) && $this->settings['payment_part_payments_confirmation_translation_' . $booking_data['language']] ? $this->settings['payment_part_payments_confirmation_translation_' . $booking_data['language']] : (isset($this->settings['payment_part_payments_confirmation']) && $this->settings['payment_part_payments_confirmation'] ? $this->settings['payment_part_payments_confirmation'] : __('The form was successfully submitted.', 'wp-booking-system'))
                ];

                add_option('booking-confirmation-' . $transient_id, array('booking_id' => $booking_data['booking_id'], 'confirmation' => $confirmation, $this->slug . '_payment_id' => $payment_intent_id));
                delete_option($lock_name);
                return false;
            }

            // Delete the transient first to prevent double bookings
            delete_option($transient_id);

            // Exit if we don't have a payment intent id
            if(!$payment_intent_id){
                return false;
            }

            // Add the payment ID to the $post_data
            $booking_data['post_data']['wpbs-' . $this->slug . '-payment-id'] = $payment_intent_id;

            // Finally submit the form, saving booking details, sending emails, etc
            $handler = new WPBS_Form_Handler($booking_data['post_data'], $booking_data['form_id'], $booking_data['form_args'], $booking_data['form_fields'], $booking_data['calendar_id']);
            $response = $handler->get_response();

            $confirmation['type'] = $response['confirmation_type'];

            // Handle the form confirmation
            if ($response['confirmation_type'] == 'redirect') {

                // Redirect to a page
                $confirmation['redirect_url'] = $response['confirmation_redirect_url'];
            } else {

                // Create the output containing the confirmation message and the tracking script
                $confirmation_message = (!empty($response['confirmation_message'])) ? $response['confirmation_message'] : '<p>The form was successfully submitted.</p>';

                $confirmation['message'] = '<div class="wpbs-form-confirmation-message" id="wpbs-payment-confirmation-message">' . $confirmation_message . '</div>';

                if (isset($response['tracking_script']) && !empty($response['tracking_script'])) {
                    $confirmation['message'] .= '<script>' . $response['tracking_script'] . '</script>';
                }
            }

            // Save the response so we can display it on the confirmation page.
            add_option('booking-confirmation-' . $transient_id, array('booking_id' => $response['booking_id'], 'confirmation' => $confirmation, $this->slug . '_payment_id' => $payment_intent_id));
        } finally {
            delete_option($lock_name);
        }
    }


    /**
     * AJAX function to get the payment status while we're on the confirmation page.
     * 
     */
    abstract public function payment_confirmation_get_payment_status();

    public function payment_confirmation_get_payment_status_wrap()
    {
        if (!isset($_POST['data'])) {
            return false;
        }

        $data = json_decode(stripslashes($_POST['data']), true);

        if (!isset($data['wpbs-' . $this->slug . '-confirmation'])) {
            return false;
        }

        $this->payment_confirmation_get_payment_status();
    }

    /**
     * Maybe create the payment on the Thank You page, depending on the payment response
     * 
     */
    abstract public function maybe_create_booking_on_thank_you_page();

    public function thank_you_page_actions()
    {
        // Check if we're on the confirmation page
        if (!isset($_GET['wpbs-' . $this->slug . '-confirmation'])) {
            return false;
        }
        $this->maybe_create_booking_on_thank_you_page();
    }

    /**
     * Ignore reCaptcha on confirmation screen
     *
     */
    public function validate_recaptcha($response, $form_data)
    {
        if (isset($form_data['wpbs-' . $this->slug . '-confirmation-loaded']) && $form_data['wpbs-' . $this->slug . '-confirmation-loaded'] == '1') {
            return true;
        }
        return $response;
    }

    /**
     * Register Payment Method
     *
     * @param array
     *
     */
    public function register_payment_method($payment_methods)
    {
        $payment_methods[$this->slug] = $this->name;
        return $payment_methods;
    }

    /**
     * Default form values
     *
     */
    public function defaults_labels()
    {
        return array(
            'display_name' => $this->display_name,
            'description' => $this->description,
        );
    }

    /**
     * Get the payment methods name
     *
     */
    public function form_outputter_payment_method_name($active, $language)
    {

        if (!empty($this->settings['payment_' . $this->slug . '_name_translation_' . $language])) {
            return $this->settings['payment_' . $this->slug . '_name_translation_' . $language];
        }
        if (!empty($this->settings['payment_' . $this->slug . '_name'])) {
            return $this->settings['payment_' . $this->slug . '_name'];
        }
        return $this->defaults_labels()['display_name'];
    }

    /**
     * Get the payment methods description
     *
     */
    public function form_outputter_payment_method_description($active, $language)
    {

        if (!empty($this->settings['payment_' . $this->slug . '_description_translation_' . $language])) {
            return $this->settings['payment_' . $this->slug . '_description_translation_' . $language];
        }
        if (!empty($this->settings['payment_' . $this->slug . '_description'])) {
            return $this->settings['payment_' . $this->slug . '_description'];
        }
        return $this->defaults_labels()['description'];
    }

    /**
     * Add the submenu to the Payments Tab
     *
     */
    public function settings_page_tab($tabs)
    {
        $tabs[$this->slug] = $this->name;
        return $tabs;
    }

    /**
     * Settings page content
     * 
     */
    abstract public function settings_page_content($defaults);

    public function settings_page_content_wrap()
    {
        $defaults = $this->defaults_labels();
        $this->settings_page_content($defaults);
    }

    /**
     * Send payment error emails
     * 
     */
    public function payment_failed_action($booking_id)
    {

        // Do not sent the same notification multiple times.
        if (wpbs_get_booking_meta($booking_id, 'payment_failed_notification_' . wp_date("Ymdhi"))) {
            return;
        }

        wpbs_add_booking_meta($booking_id, 'payment_failed_notification_' . wp_date("Ymdhi"), true);

        // Send Payment Failed emails
        $emails = new WPBS_Email_Notifications($booking_id);
        $emails->send_payment_failed_emails();

        // Log dashboard notification
        wpbs_insert_notification([
            'notification_group' => 'notification',
            'booking_id' => $booking_id,
            'notification_type' => 'payment_failed',
            'notification_status' => 'active',
            'date_created' => date('Y-m-d H:i:s', current_time('timestamp'))
        ]);
    }

    /**
     * Check if the payment method is enabled or not.
     * 
     */
    public function is_enabled()
    {
        if (isset($this->settings['payment_' . $this->slug . '_enable']) && $this->settings['payment_' . $this->slug . '_enable'] == 'on') {
            return true;
        }
        return false;
    }

    /**
     * Getter
     *
     * @param string $property
     *
     */
    public function get($property = '')
    {

        if (method_exists($this, 'get_' . $property))
            return $this->{'get_' . $property}();
        else
            return $this->$property;
    }
}
