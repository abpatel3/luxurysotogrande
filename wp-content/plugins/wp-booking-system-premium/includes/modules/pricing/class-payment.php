<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The main class for the Payment
 *
 */
class WPBS_Payment extends WPBS_Base_Object
{

    /**
     * The Id of the payment
     *
     * @access protected
     * @var    int
     *
     */
    protected $id;

    /**
     * The booking id of the payment
     *
     * @access protected
     * @var    int
     *
     */
    protected $booking_id;

    /**
     * The payment gateway that was used
     *
     * @access protected
     * @var    string
     *
     */
    protected $gateway;

    /**
     * The ID of the order
     *
     * @access protected
     * @var    string
     *
     */
    protected $order_id;

    /**
     * The status of the order
     *
     * @access protected
     * @var    string
     *
     */
    protected $order_status;

    /**
     * The payment details
     *
     * @access protected
     * @var    string
     *
     */
    protected $details;

    /**
     * The date when the payment was created
     *
     * @access protected
     * @var    string
     *
     */
    protected $date_created;

    /**
     * The prices, stored in $details['prices'];
     *
     * @access protected
     * @var array
     */
    public $prices;

    /**
     * VAT
     * 
     * @access protected
     * @var object
     * 
     */
    public $vat;

    /**
     * The constructor
     *
     */
    public function __construct($object = false)
    {

        if ($object === false) {
            $object = new stdClass();
        }

        parent::__construct($object);

        // Set the prices if available
        if (isset($this->details['price']) && $this->details['price']) {
            $this->prices = $this->details['price'];
        }

        $this->set_currency();

        $this->backwards_compatibility();
    }

    /**
     * Make sure nothing breaks
     * 
     */
    private function backwards_compatibility()
    {

        // Set the default multiplication for old bookings.
        if (!isset($this->prices['events']['multiplication'])) {
            $this->prices['events']['multiplication'] = 1;
        }
    }

    /**
     * Set the currency
     *
     */
    public function set_currency()
    {

        if (!isset($this->prices['currency']) || empty($this->prices['currency'])) {
            $this->prices['currency'] = wpbs_get_currency();
        }
    }

    /**
     * Get the currency
     *
     * @return string
     */
    public function get_currency()
    {
        return $this->prices['currency'];
    }

    /**
     * Get the currency
     *
     * @return string
     */
    public function get_display_currency()
    {
        return apply_filters('wpbs_display_currency', $this->get_currency());
    }

    /**
     * Get the total value of the order
     *
     * @return float
     */
    public function get_total()
    {
        if (!isset($this->prices['total'])) {
            return 0;
        }
        return round($this->prices['total'], 2);
    }

    /**
     * Get the subtotal value of the order
     *
     * @return float
     */
    public function get_subtotal()
    {
        if (!isset($this->prices['subtotal'])) {
            return 0;
        }
        return round($this->prices['subtotal'], 2);
    }

    /**
     * Check whether the order is paid or not
     *
     * @return bool
     */
    public function is_paid()
    {
        return isset($this->prices['paid']) && $this->prices['paid'] == true;
    }

    /**
     * Get the payment method
     *
     * @return string
     */
    public function get_payment_method()
    {
        return $this->prices['payment_method'];
    }

    /**
     * Check if it's a part payment
     *
     * @return bool
     */
    public function is_part_payment()
    {
        return isset($this->prices['part_payments']);
    }

    /**
     * Get the language
     *
     * @return string
     */
    public function get_language()
    {
        return $this->prices['language'];
    }

    /**
     * Get the value of the first payment (deposit)
     *
     * @return float
     *
     */
    public function get_total_first_payment()
    {
        if (!isset($this->prices['part_payments'])) {
            return false;
        }
        if (!isset($this->prices['part_payments']['first_payment'])) {
            return false;
        }
        return $this->prices['part_payments']['first_payment'];
    }

    /**
     * Get the value of the second payment (final payment)
     *
     * @return float
     *
     */
    public function get_total_second_payment()
    {
        if (!isset($this->prices['part_payments'])) {
            return false;
        }
        if (!isset($this->prices['part_payments']['second_payment'])) {
            return false;
        }
        return $this->prices['part_payments']['second_payment'];
    }

    /**
     * Check if the deposit was paid
     *
     * @return bool
     */
    public function is_deposit_paid()
    {
        return isset($this->details['part_payments']['deposit']) && $this->details['part_payments']['deposit'] === true;
    }

    /**
     * Check if the final payment was paid
     *
     * @return bool
     */
    public function is_final_payment_paid()
    {
        return isset($this->details['part_payments']['final_payment']) && $this->details['part_payments']['final_payment'] === true;
    }

    /**
     * Get the payment final payment method
     *
     * @return string
     */
    public function get_final_payment_method()
    {
        return isset($this->prices['part_payments']['method']) ? $this->prices['part_payments']['method'] : 'initial';
    }

    /**
     * Get the payment gateway used
     *
     * @return float
     */
    public function get_payment_gateway()
    {
        return isset($this->prices['payment_method']) ? $this->prices['payment_method'] : '';
    }

    /**
     * Get the refund status
     *
     * @return bool
     */
    public function get_refund_status()
    {
        return isset($this->details['refund_status']) ? $this->details['refund_status'] : 'not_refunded';
    }

    /**
     * Check if the payment has a security deposit
     *
     * @return bool
     */
    public function has_security_deposit()
    {
        return isset($this->prices['has_security_deposit']) && $this->prices['has_security_deposit'] === true;
    }

    /**
     * Calculate the prices based on selected dates and form input
     *
     * @param array $post_data
     * @param WPBS_Form @form
     * @param array $form_args
     * @param array $form_fields
     *
     * @return array
     *
     */
    public function calculate_prices($post_data, $form, $form_args, $form_fields)
    {

        $settings = get_option('wpbs_settings');

        $this->vat = new WPBS_VAT($form->get('id'), $post_data);

        $this->prices['has_vat'] = $this->vat->is_enabled();
        $this->prices['vat_display_only'] = isset($settings['payment_vat_display_only']) && $settings['payment_vat_display_only'] == 'on' ? true : false;

        $this->prices['vat_percentage'] = false;
        if ($this->vat->is_enabled()) {
            $this->prices['vat_percentage'] = $this->vat->get_percentage();
        }

        $this->prices['is_payment_screen'] = isset($post_data['payment_screen']) && $post_data['payment_screen'] ? true : false;

        if (isset($post_data['post_data'])) {
            parse_str($post_data['post_data'], $post);
            $this->prices['post_id'] = $post['wpbs-post-id'];
        } elseif (isset($post_data['form_data'])) {
            parse_str($post_data['form_data'], $post);
            $this->prices['post_id'] = $post['wpbs-post-id'];
        }

        $this->prices['total'] = 0;

        if(isset($form_args['manual_booking']) && $form_args['manual_booking']){
            $this->prices['manual_booking'] = true;
        }

        $language = $form_args['language'];
        $this->prices['language'] = $language;

        $this->prices['form_id'] = $form->get('id');

        // Get payment method
        $this->prices['payment_method'] = '';

        foreach ($form_fields as $field) {
            if ($field['type'] != 'payment_method') {
                continue;
            }

            if (!isset($field['user_value'])) {
                continue;
            }

            $this->prices['payment_method'] = $field['user_value'];
        }

        // Set dates
        $start_date = wpbs_convert_js_to_php_timestamp($post_data['calendar']['start_date']);
        $end_date = wpbs_convert_js_to_php_timestamp($post_data['calendar']['end_date']);

        $conditional_logic = new WPBS_Form_Conditional_Logic($form_fields, $start_date, $end_date, ['selection_style' => $form_args['selection_style'], 'calendar_id' => $post_data['calendar']['id']]);
        $conditional_logic_hidden_fields = $conditional_logic->get_hidden_fields();

        $this->prices = apply_filters('wpbs_payment_prices', $this->prices, $post_data);

        // Get default price
        $calendar_id = absint(!empty($post_data['calendar']['id']) ? $post_data['calendar']['id'] : 0);
        $this->prices['calendar_id'] = $calendar_id;

        $default_price = wpbs_get_calendar_meta($calendar_id, 'default_price', true);

        $default_price = apply_filters('wpbs_pricing_item_modifier', $default_price, $this->prices, 'default_price');

        // Product name

        $product_name = wpbs_get_translated_form_meta($form->get('id'), 'product_name', $language);

        if (empty($product_name)) {
            $product_name = (isset($settings['payment_product_name_translation_' . $language]) && !empty($settings['payment_product_name_translation_' . $language])) ? $settings['payment_product_name_translation_' . $language] : ((isset($settings['payment_product_name']) && !empty($settings['payment_product_name'])) ? $settings['payment_product_name'] : __('Item', 'wp-booking-system'));
        }

        $product_name = apply_filters('wpbs_pricing_product_name', $product_name, $calendar_id, $form->get('id'), $language, $this);

        // Get selection style
        $selection_style = $post_data['form']['selection_style'];

        $this->prices['selection_style'] = $selection_style;

        /**
         * Calculate Events Price
         */

        // Set defaults
        $events_price = 0;
        $quantity = 0;

        // Start Date
        $events_begin = new DateTime();
        $events_begin->setTimestamp($start_date);

        // End date
        $events_end = new DateTime();
        $events_end->setTimestamp($end_date);
        if ($selection_style == 'normal') {
            $events_end->modify('+1 day'); // Add +1 day to correct the interval
        }

        if ($selection_style == 'split' && $start_date == $end_date) {
            $events_end->modify('+1 day');
        }

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($events_begin, $interval, $events_end);

        foreach ($period as $event_date) {
            // Check for custom price
            $events = wpbs_get_events(array('calendar_id' => $calendar_id, 'date_day' => $event_date->format('d'), 'date_month' => $event_date->format('m'), 'date_year' => $event_date->format('Y')));

            if (!empty($events) && (!empty($events[0]->get('price')) || $events[0]->get('price') === 0)) {
                $event_price = apply_filters('wpbs_pricing_item_modifier', $events[0]->get('price'), $this->prices, 'event_price');
            } else {
                $event_price = $default_price;
            }

            if (is_numeric($event_price)) {
                $events_price += $event_price;
            }

            $this->prices['events']['individual_days'][$event_date->format('Ymd')] = round((float) $event_price, 2);

            //Increase quantity
            $quantity++;
        }

        // Check for multiplication
        $this->prices['events']['multiplication'] = 1;

        if (wpbs_get_form_meta($form->get('id'), 'multiplication_field', true)) {
            $multiplication_field_id = wpbs_get_form_meta($form->get('id'), 'multiplication_field', true);

            foreach ($form_fields as $field) {
                if ($field['id'] != $multiplication_field_id) {
                    continue;
                }

                if (!isset($field['user_value'])) {
                    continue;
                }

                $user_value = self::get_multiplication_field_value($field);

                $this->prices['events']['multiplication'] = max(1, abs((float) $user_value));
            }

            $events_price = $events_price * $this->prices['events']['multiplication'];
        }

        $events_price = apply_filters('wpbs_pricing_events_price', $events_price, $this->prices, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date);

        $this->prices['events']['price_with_vat'] = $events_price;

        $events_price = $this->vat->deduct_vat($events_price);

        $events_price = round($events_price, 2);

        $this->prices['total'] += $events_price;
        $this->prices['start_date'] = $start_date;
        $this->prices['end_date'] = $end_date;

        $this->prices['quantity'] = $quantity;
        $this->prices['default_price'] = $default_price;

        $this->prices['events']['name'] = $product_name;
        $this->prices['events']['price'] = $events_price;

        $this->prices = apply_filters('wpbs_get_checkout_price_after_events', $this->prices, $this, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date);

        /**
         * Calculate Extras Prices
         */

        foreach ($form_fields as $form_field) {

            if (!wpbs_form_field_is_product($form_field['type'])) {
                continue;
            }

            if (empty($form_field['user_value'])) {
                continue;
            }

            if (!is_array($form_field['user_value']) && trim($form_field['user_value']) == '|') {
                continue;
            }

            // Check if field is required by conditional logic
            if (array_key_exists($form_field['id'], $conditional_logic_hidden_fields)) {
                continue;
            }

            // Check if is an array or single value
            $options = (!is_array($form_field['user_value'])) ? array($form_field['user_value']) : $form_field['user_value'];

            // Check if it's a number product field
            if ($form_field['type'] == 'product_number') {
                if (empty($options)) {
                    continue;
                }

                if (!is_array($options)) {
                    continue;
                }

                if (!isset($form_field['values']['default']['pricing'])) {
                    continue;
                }

                if (empty($form_field['values']['default']['pricing']) && $form_field['values']['default']['pricing'] !== '0') {
                    continue;
                }

                if (isset($form_field['values'][$language]['label']) && !empty($form_field['values'][$language]['label'])) {
                    $label = $form_field['values'][$language]['label'];
                } else {
                    $label = $form_field['values']['default']['label'];
                }

                $options = [$form_field['values']['default']['pricing'] * array_shift($options) . '|' . $label];
            }

            // Calculate the number of days the discount is applicable to
            $addition_multiplication = $this->prices['quantity'];

            // Check if we have a date range
            if (isset($form_field['values']['default']['date_range']) && !empty($form_field['values']['default']['date_range'])) {



                $date_range = $form_field['values']['default']['date_range'];
                list($date_range_start, $date_range_end) = explode("|", $date_range);

                // Make the dates a DateTime object
                $date_range_start = DateTime::createFromFormat('Y-m-d H:i:s', $date_range_start . ' 00:00:00');
                $date_range_end = DateTime::createFromFormat('Y-m-d H:i:s', $date_range_end . ' 00:00:00');

                // Check if the dates are valid
                if ($date_range_start && $date_range_end) {

                    // Clone the event starting date in case we need to modify it
                    $date_range_events_begin = clone $events_begin;
                    $date_range_events_end = clone $events_end;

                    // Check recurrence
                    if ($form_field['values']['default']['date_range_type'] == 'yearly') {
                        $date_range_events_begin->setDate(date('Y', $start_date), $date_range_events_begin->format('m'), $date_range_events_begin->format('d'));
                        $date_range_events_end->setDate(date('Y', $end_date), $date_range_events_end->format('m'), $date_range_events_end->format('d'));
                        $date_range_start->setDate(date('Y', $start_date), $date_range_start->format('m'), $date_range_start->format('d'));
                        $date_range_end->setDate(date('Y', $start_date), $date_range_end->format('m'), $date_range_end->format('d'));

                        if ($date_range_start > $date_range_end) {
                            if ($date_range_events_begin < $date_range_start && $date_range_events_end < $date_range_start) {
                                $date_range_start->modify('-1 year');
                            } else {
                                $date_range_end->modify('+1 year');
                            }
                        }
                    }

                    // Check if date is in range
                    if (!(wpbs_is_date_in_range($date_range_start, $date_range_end, $date_range_events_begin) || wpbs_is_date_in_range($date_range_start, $date_range_end, $date_range_events_end) || wpbs_is_date_in_range($date_range_events_begin, $date_range_events_end, $date_range_start))) {
                        continue;
                    }
                }

                // Count the applicable number of days
                $addition_multiplication = 0;

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($events_begin, $interval, $events_end);

                foreach ($period as $date) {
                    if ($date_range_start && $date_range_end) {
                        if ($date >= $date_range_start && $date <= $date_range_end) {
                            $addition_multiplication++;
                        }
                    } elseif ($date_range_start && !$date_range_end) {
                        if ($date >= $date_range_start) {
                            $addition_multiplication++;
                        }
                    } elseif (!$date_range_start && $date_range_end) {
                        if ($date <= $date_range_end) {
                            $addition_multiplication++;
                        }
                    } else {
                        $addition_multiplication++;
                    }
                }
            }

            // Loop
            foreach ($options as $option) {

                if (empty($option)) {
                    continue;
                }

                // Explode and get key value pairs
                list($price, $value) = explode('|', $option);

                if (empty($price) && $price !== '0') {
                    continue;
                }

                $price = apply_filters('wpbs_pricing_item_modifier', $price, $this->prices, 'extras_price');

                // Check addition
                $addition = isset($form_field['values']['default']['pricing_type']) ? $form_field['values']['default']['pricing_type'] : 'per_booking';
                $extra_price = ($addition == 'per_day') ? $price * $addition_multiplication : $price;

                // Multiply by another field
                $multiplication_id = isset($form_field['values']['default']['multiplication']) ? absint($form_field['values']['default']['multiplication']) : false;
                $multiplication = false;

                if ($multiplication_id !== false) {
                    foreach ($form_fields as $multiplication_form_field) {
                        if ($multiplication_form_field['id'] != $multiplication_id) {
                            continue;
                        }

                        $multiplication_value = self::get_multiplication_field_value($multiplication_form_field);

                        $multiplication = max(0, round(abs($multiplication_value), 2));

                        $extra_price = $extra_price * $multiplication;
                    }
                }

                $original_line_label = $value;

                if (isset($form_field['values'][$language]['line_label']) && !empty($form_field['values'][$language]['line_label'])) {
                    $line_label = $form_field['values'][$language]['line_label'];
                } elseif (isset($form_field['values']['default']['line_label']) && !empty($form_field['values']['default']['line_label'])) {
                    $line_label = $form_field['values']['default']['line_label'];
                } else {
                    $line_label = '%%';
                }

                $line_label = str_replace('%%', $original_line_label, $line_label);

                $custom_vat = (isset($form_field['values']['default']['custom_vat']) && (!empty($form_field['values']['default']['custom_vat']) || $form_field['values']['default']['custom_vat'] !== "") ? floatval($form_field['values']['default']['custom_vat']) : false);

                // Filter extra fields
                $extra = apply_filters('wpbs_get_checkout_price_extra', array(
                    'field_id' => $form_field['id'],
                    'label' => $line_label,
                    'original_label' => $original_line_label,
                    'price' => $this->vat->deduct_vat($price, false, $custom_vat),
                    'vat_percentage' => $custom_vat !== false ? $custom_vat : $this->prices['vat_percentage'],
                    'price_with_vat' => $price,
                    'addition' => $addition,
                    'addition_multiplication' => $addition_multiplication,
                    'multiplication' => $multiplication,
                    'total' => $extra_price,
                    'user_value' => $form_field['user_value'],
                    'extra_type' => $form_field['type']
                ), $this->prices, $form_fields, $calendar_id, $form_args, $form, $start_date, $end_date, $post_data);

                $extra['total_with_vat'] = $extra['total'];
                $extra['total'] = $this->vat->deduct_vat($extra['total'], true, $custom_vat);

                $extra['total'] = round($extra['total'], 2);

                // Add to final output
                $this->prices['extras'][] = $extra;

                // Increment total price
                $this->prices['total'] += $extra['total'];
            }
        }

        $this->prices['subtotal'] = $this->prices['total'];

        $this->prices = apply_filters('wpbs_get_checkout_price_before_subtotal', $this->prices, $this, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date, $post_data);

        if ($this->prices['total'] < 0) {
            $this->prices['total'] = 0;
        }

        $this->prices['subtotal'] = $this->prices['total'];



        /**
         * Calculate Taxes
         *
         */
        $taxes = array();

        $this->prices = apply_filters('wpbs_get_checkout_price_before_vat', $this->prices, $this, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date, $post_data);

        /**
         * VAT
         *
         */
        if ($this->vat->is_enabled()) {
            $vat_breakdown = $this->vat->get_vat_amount_breakdown();
            $vat_names = apply_filters('wpbs_custom_vat_names', []);
            foreach ($vat_breakdown as $percentage => $vat) {

                // Do not allow negative VAT
                if ($vat < 0) {
                    continue;
                }

                $percentage /= 100;
                $name = isset($vat_names[$percentage]) ? $vat_names[$percentage] : $this->vat->get_name($language) . ' - ' . $percentage . '%';
                $taxes[] = array(
                    'name' => $name,
                    'percentage' => $percentage,
                    'fixed_amount' => 0,
                    'calculation' => 'per_booking',
                    'value' => $vat,
                    'type' => 'vat',
                );
            }

            if (!isset($settings['payment_vat_display_only']) || empty($settings['payment_vat_display_only'])) {
                $this->prices['total'] += $this->vat->get_vat_amount();
            }
        }

        $this->prices = apply_filters('wpbs_get_checkout_price_before_taxes', $this->prices, $this, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date, $post_data);

        /**
         * Taxes
         *
         */
        if (isset($settings['payment_tax_enable']) && $settings['payment_tax_enable'] == 'on' && isset($settings['payment_tax_name'])) {

            for ($i = 0; $i < count($settings['payment_tax_name']); $i++) {

                // Check Calendars
                if (isset($settings['payment_tax_calendars'][$i]) && !empty($settings['payment_tax_calendars'][$i]) && !in_array($calendar_id, (array) $settings['payment_tax_calendars'][$i])) {
                    continue;
                }

                // Check Forms
                if (isset($settings['payment_tax_forms'][$i]) && !empty($settings['payment_tax_forms'][$i]) && !in_array($form->get('id'), (array) $settings['payment_tax_forms'][$i])) {
                    continue;
                }

                // Check if there's a value set
                if (empty($settings['payment_tax_percentage'][$i]) && (!isset($settings['payment_tax_fixed_amount'][$i]) || empty($settings['payment_tax_fixed_amount'][$i]))) {
                    continue;
                }

                $inclusion = isset($settings['payment_tax_inclusion'][$i]) && $settings['payment_tax_inclusion'][$i] ? $settings['payment_tax_inclusion'][$i] : 'start_date';

                $tax_start_date = clone $events_begin;
                $tax_end_date = clone $events_end;

                // Check if there's a date range set
                if (isset($settings['payment_tax_start_period'][$i]) && !empty($settings['payment_tax_start_period'][$i]) && isset($settings['payment_tax_end_period'][$i]) && !empty($settings['payment_tax_end_period'][$i])) {

                    $validity_from = DateTime::createFromFormat('d/m/Y', $settings['payment_tax_start_period'][$i]);
                    $validity_from->setTime(0, 0, 0);

                    $validity_to = DateTime::createFromFormat('d/m/Y', $settings['payment_tax_end_period'][$i]);
                    $validity_to->setTime(0, 0, 0);

                    $validity_period_included = false;

                    if ($inclusion == 'entire_date' && (wpbs_is_date_in_range($validity_from, $validity_to, $tax_start_date) && wpbs_is_date_in_range($validity_from, $validity_to, $tax_end_date))) {
                        $validity_period_included = true;
                    }

                    if ($inclusion == 'any_date' && (wpbs_is_date_in_range($validity_from, $validity_to, $tax_start_date) || wpbs_is_date_in_range($validity_from, $validity_to, $tax_end_date))) {
                        $validity_period_included = true;
                    }

                    if ($inclusion == 'start_date' && wpbs_is_date_in_range($validity_from, $validity_to, $tax_start_date)) {
                        $validity_period_included = true;
                    }

                    // Skip if date range doesn't match
                    if ($validity_period_included === false) {
                        continue;
                    }
                }
                // Calculate the number of days the discount is applicable to
                $days_applicable_num = 0;
                $days_applicable = array();
                $days_total = 0;

                $tax_start_date = clone $events_begin;
                $tax_end_date = clone $events_end;

                // Set loop interval
                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($tax_start_date, $interval, $tax_end_date);

                foreach ($period as $date) {
                    if (isset($validity_from)) {
                        if (wpbs_is_date_in_range($validity_from, $validity_to, $date)) {
                            $days_applicable_num++;
                            $days_applicable[] = $date;
                        }
                    } else {
                        $days_applicable_num++;
                        $days_applicable[] = $date;
                    }

                    $days_total++;
                }

                // Set the base name
                $tax_name = (isset($settings['payment_tax_name_translation_' . $language][$i]) && !empty($settings['payment_tax_name_translation_' . $language][$i])) ? $settings['payment_tax_name_translation_' . $language][$i] : $settings['payment_tax_name'][$i];

                // Calculate tax
                if (isset($settings['payment_tax_type'][$i]) && $settings['payment_tax_type'][$i] == 'fixed_amount') {
                    $tax_percentage = false;
                    $tax_value = floatval($settings['payment_tax_fixed_amount'][$i]);
                    $tax_value = apply_filters('wpbs_pricing_item_modifier', $tax_value, $this->prices, 'tax_value');

                    if ($settings['payment_tax_calculation'][$i] == 'per_day') {

                        $differenceObject = $tax_end_date->diff($tax_start_date);
                        $difference = $differenceObject->days;

                        if ($form_args['selection_style'] == 'normal') {
                            $difference++;
                        }

                        if ($days_applicable_num == 0) {
                            continue;
                        }

                        $multiplication_number_of_days = (int) min($days_applicable_num, $difference);
                        $multiplication_number_of_days = apply_filters('wpbs_taxes_multiplication_number_of_days', $multiplication_number_of_days, $this);

                        $tax_value = $multiplication_number_of_days * $tax_value;
                    }

                    // Check multiplication field.
                    $tax_multiplication_field = (isset($settings['payment_tax_multiplication'][$i]) && !empty($settings['payment_tax_multiplication'][$i])) ? $settings['payment_tax_multiplication'][$i] : false;

                    if ($tax_multiplication_field !== false) {
                        list($tax_form_id, $tax_form_field_id) = explode('_', $tax_multiplication_field);

                        $tax_multiplication_value = false;

                        if ($form->get('id') == $tax_form_id) {
                            foreach ($form_fields as $form_field) {
                                if ($form_field['id'] != $tax_form_field_id) {
                                    continue;
                                }

                                $tax_multiplication_value = max(0, abs((float) self::get_multiplication_field_value($form_field)));
                            }
                        }

                        if ($tax_multiplication_value !== false) {
                            $tax_value = $tax_value * $tax_multiplication_value;
                        }
                    }
                } else {
                    $tax_source = $this->prices['subtotal'];
                    $tax_percentage = floatval($settings['payment_tax_percentage'][$i]);

                    if (isset($settings['payment_tax_application'][$i]) && $settings['payment_tax_application'][$i] == 'calendar') {

                        if (isset($this->prices['events']['discounted_value'])) {
                            $tax_source = $this->prices['events']['price'] + $this->prices['events']['discounted_value'];
                        } else {
                            $tax_source = $this->prices['events']['price'];
                        }
                    } elseif (isset($settings['payment_tax_application'][$i]) && $settings['payment_tax_application'][$i] == 'form') {
                        $tax_source = 0;
                        if (isset($this->prices['extras'])) {
                            foreach ($this->prices['extras'] as $form_tax_product) {
                                $tax_source += $form_tax_product['total'];
                            }
                        }
                    }

                    if ($this->prices['subtotal'] == 0) {
                        $tax_source = 0;
                    }

                    $tax_source = apply_filters('wpbs_get_checkout_tax_source', $tax_source, $tax_name, $this->prices, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date);

                    $tax_value = (($tax_source * $tax_percentage / $days_total) * $days_applicable_num) / 100;

                    // Check multiplication field.
                    $tax_multiplication_field = (isset($settings['payment_tax_multiplication'][$i]) && !empty($settings['payment_tax_multiplication'][$i])) ? $settings['payment_tax_multiplication'][$i] : false;

                    if ($tax_multiplication_field !== false) {
                        list($tax_form_id, $tax_form_field_id) = explode('_', $tax_multiplication_field);

                        $tax_multiplication_value = false;

                        if ($form->get('id') == $tax_form_id) {
                            foreach ($form_fields as $form_field) {
                                if ($form_field['id'] != $tax_form_field_id) {
                                    continue;
                                }

                                $tax_multiplication_value = max(0, abs((float) self::get_multiplication_field_value($form_field)));
                            }
                        }

                        if ($tax_multiplication_value !== false) {
                            $tax_value = $tax_value * $tax_multiplication_value;
                        }
                    }

                    $add_percentage_to_tax_name = apply_filters('wpbs_payment_add_percentage_to_tax_name', true, $tax_name);

                    if ($add_percentage_to_tax_name) {
                        $tax_name .= ' - ' . $tax_percentage . '%';
                    }
                }

                $tax_value = apply_filters('wpbs_get_checkout_tax', $tax_value, $tax_name, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date);

                $tax_value = round($tax_value, 2);

                $tax_data = apply_filters('wpbs_get_checkout_tax_data', array(
                    'name' => $tax_name,
                    'percentage' => $tax_percentage,
                    'fixed_amount' => isset($settings['payment_tax_fixed_amount'][$i]) ? $settings['payment_tax_fixed_amount'][$i] : 0,
                    'calculation' => isset($settings['payment_tax_calculation'][$i]) ? $settings['payment_tax_calculation'][$i] : 'per_booking',
                    'value' => $tax_value,
                    'multiplication_field_id' => (isset($tax_multiplication_field) ? $tax_multiplication_field : ''),
                    'multiplication_value' => (isset($tax_multiplication_value) ? $tax_multiplication_value : ''),
                    'type' => 'tax',
                ), $this->prices);

                $taxes[] = $tax_data;

                $this->prices['total'] += $tax_data['value'];
            }
        }

        if (!empty($taxes)) {
            $this->prices['taxes'] = $taxes;
        }

        $this->prices = apply_filters('wpbs_get_checkout_price_after_total', $this->prices, $post_data, $calendar_id, $form_args, $form, $form_fields, $this);

        $this->prices['total'] = round($this->prices['total'], 2);

        $this->prices = apply_filters('wpbs_get_checkout_price_after', $this->prices, $post_data, $calendar_id, $form_args, $form, $form_fields);

        return $this->prices;
    }

    /**
     * Generate an array containing all the line items
     *
     * @param bool $unfiltered - false = remove empty array items
     *
     * @return array(label,value,tooltip,class,description,quantity,individual_price,type)
     *
     */
    public function get_line_items($unfiltered = false)
    {

        $line_items = array();

        $line_items['events'] = array(
            'label' => $this->prices['events']['name'] . ' <span class="wpbs-line-item-quantity">&times; ' . $this->prices['quantity'] . ($this->prices['events']['multiplication'] && $this->prices['events']['multiplication'] > 1 ? ' &times; ' . $this->prices['events']['multiplication'] : '') . '</span>',
            'label_raw' => $this->prices['events']['name'],
            'value' => wpbs_get_formatted_price($this->prices['events']['price'], $this->get_display_currency(), true),
            'tooltip' => wpbs_get_formatted_price(round($this->prices['events']['price'] / (max(1, $this->prices['quantity']) * max(1, $this->prices['events']['multiplication'])), 2), $this->get_display_currency()),
            'quantity' => $this->prices['quantity'] * $this->prices['events']['multiplication'],
            'price' => $this->prices['events']['price'],
            'price_with_vat' => isset($this->prices['events']['price_with_vat']) ? $this->prices['events']['price_with_vat'] : false,
            'multiplication' => $this->prices['events']['multiplication'],
            'individual_price' => $this->prices['events']['price'] / (max(1, $this->prices['quantity']) * max(1, $this->prices['events']['multiplication'])),
            'individual_price_with_vat' => $this->prices['events']['price_with_vat'] / (max(1, $this->prices['quantity']) * max(1, $this->prices['events']['multiplication'])),
            'type' => 'event',
            'prices_per_day' => isset($this->prices['events']['individual_days']) ? $this->prices['events']['individual_days'] : array(),
            'class' => 'wpbs-pricing-table-events wpbs-line-item-price-' . $this->prices['events']['price'],
            'editable' => true,
        );

        // Extras
        if (isset($this->prices['extras'])) {
            foreach ($this->prices['extras'] as $i => $item) {
                $line_items[] = array(
                    'label' => $item['label'] . ' <span class="wpbs-line-item-quantity">' . $this->get_extra_quantity_string($item) . '</span>',
                    'label_raw' => $item['label'],
                    'value' => wpbs_get_formatted_price($item['total'], $this->get_display_currency(), true),
                    'quantity' => ($item['addition'] == 'per_day' ? ($item['multiplication'] !== false ? $this->prices['quantity'] * $item['multiplication'] : $this->prices['quantity']) : ($item['multiplication'] !== false ? $item['multiplication'] : 1)),
                    'price' => $item['total'],
                    'price_with_vat' => isset($item['total_with_vat']) ? $item['total_with_vat'] : false,
                    'individual_price' => $item['price'],
                    'individual_price_with_vat' => isset($item['price_with_vat']) ? $item['price_with_vat'] : false,
                    'vat_percentage' => isset($item['vat_percentage']) ? $item['vat_percentage'] : false,
                    'type' => 'extra',
                    'class' => 'wpbs-pricing-table-extra wpbs-pricing-table-extra-' . sanitize_title($item['label']) . ' wpbs-line-item-price-' . $item['total'],
                    'editable' => true,
                    'field_id' => $item['field_id']
                );
            }
        }

        $line_items = apply_filters('wpbs_line_items_before_subtotal', $line_items, $this);

        $line_items['subtotal'] = array();

        // Taxes
        if (isset($this->prices['taxes'])) {
            foreach ($this->prices['taxes'] as $i => $item) {
                $line_items[] = array(
                    'label' => $item['name'],
                    'value' => wpbs_get_formatted_price($item['value'], $this->get_display_currency(), true),
                    'price' => $item['value'],
                    'type' => (isset($item['type']) && $item['type'] ? $item['type'] : 'tax'),
                    'class' => 'wpbs-pricing-table-tax wpbs-line-item-price-' . $item['value'],
                    'editable' => true,
                );
            }
        }

        $line_items = apply_filters('wpbs_line_items_before_total', $line_items, $this);

        // Total
        $line_items['total'] = array(
            'label' => __('Total', 'wp-booking-system'),
            'value' => wpbs_get_formatted_price($this->prices['total'], $this->get_display_currency(), true),
            'price' => $this->prices['total'],
            'class' => 'wpbs-line-item-total wpbs-pricing-table-total wpbs-line-item-price-' . $this->prices['total'],
            'type' => 'total',
            'editable' => true,
        );

        $line_items = apply_filters('wpbs_line_items_after_total', $line_items, $this);

        if ($unfiltered == true) {
            return $line_items;
        }

        return array_filter($line_items);
    }

    /**
     * Returns the HTML for the pricing table displayed on the front-end
     *
     * @param string $language
     *
     * @return string
     *
     */
    public function get_pricing_table($language)
    {

        $line_items = $this->get_pricing_table_line_items($language);


        // Add date selection to "Events".
        if (wpbs_get_form_meta($this->prices['form_id'], 'show_date_in_pricing_table', true)) {
            if ($this->prices['start_date'] == $this->prices['end_date']) {
                $line_items['events']['description'] = '<strong>' . wpbs_date_i18n(get_option('date_format'), $this->prices['start_date'], $this->prices['language']) . '</strong>';
            } else {
                $line_items['events']['description'] = sprintf(wpbs_get_payment_default_string('pricing_table_dates', $this->prices['language']), '<strong>' . wpbs_date_i18n(get_option('date_format'), $this->prices['start_date'], $this->prices['language']) . '</strong>', '<strong>' . wpbs_date_i18n(get_option('date_format'), $this->prices['end_date'], $this->prices['language']) . '</strong>');
            }
        }

        $output = '';

        $output .= apply_filters('wpbs_pricing_table_before', '', $line_items, $this->prices, $language);

        // Check if part payment applicability option is enabled
        $part_payments_applicability = isset($this->prices['part_payment_applicable']) ? true : false;
        
        // Check if it's a manual booking 
        if(isset($this->prices['manual_booking'])){
            // Disable part payment applicability by default for manual bookings
            $part_payments_applicability = false;

            // But enable if for payment gateways that support it
            if(apply_filters('wpbs_payment_method_supports_manual_deposit_' . $this->prices['payment_method'], false) == true){
                $part_payments_applicability = true;
            }
        }

        $output .= '<table class="wpbs-pricing-table ' . ($part_payments_applicability ? 'wpbs-part-payment-applicable' : '') . ' wpbs-payment-method-' . ($this->prices['payment_method'] ?:  'none') . '">';

        // Table Head
        $output .= '<thead><tr><th>' . wpbs_get_payment_default_string('item', $language) . '</th><th>' . wpbs_get_payment_default_string('total', $language) . '</th></thead>';

        // Table Body
        $output .= '<tbody>';

        foreach ($line_items as $line_item_key => $line_item) {
            $output .= '<tr class="' . (isset($line_item['class']) ? $line_item['class'] : '') . '"  data-price="' . (isset($line_item['price']) && !empty($line_item['price']) ? $line_item['price'] : 0) . '">';

            $output .= '<td>';
            $output .= (isset($line_item['tooltip']) && !empty($line_item['tooltip']) ? '<span class="wpbs-abbr" title="' . $line_item['tooltip'] . '">' . (isset($line_item['label']) && !empty($line_item['label']) ? $line_item['label'] : '') . '</span>' : (isset($line_item['label']) && !empty($line_item['label']) ? $line_item['label'] : ''));
            $output .= (isset($line_item['description']) && !empty($line_item['description']) ? '<small class="wpbs-line-item-description">' . $line_item['description'] . '</small>' : '');

            // Price Breakdown
            if (wpbs_get_form_meta($this->prices['form_id'], 'show_price_breakdown_in_pricing_table', true) && $line_item_key == 'events') {
                $output .= $this->get_price_breakdown();
            }

            $output .= '</td>';

            $output .= '<td>';
            $output .= (isset($line_item['value']) && !empty($line_item['value']) ? $line_item['value'] : '');
            $output .= apply_filters('wpbs_pricing_table_line_items_price', '', $line_item);
            $output .= '</td>';

            $output .= '</tr>';
        }

        $output .= '</tbody>';

        $output .= '</table>';

        $output .= apply_filters('wpbs_pricing_table_after', '', $line_items, $this->prices, $language);

        return $output;
    }

    /**
     * Get the line items for the pricing table
     * Adds the Subtotal field as a line item
     *
     * @param string $language
     *
     * @return array
     *
     */
    public function get_pricing_table_line_items($language)
    {

        // Get line items
        $line_items = $this->get_localized_line_items($language, true);
        $settings = get_option('wpbs_settings');

        // Add subtotal

        if ($this->get_subtotal() != $this->get_total() || $this->get_subtotal() == 0 || (isset($settings['payment_vat_enable']) && $settings['payment_vat_enable'] == 'on' && isset($settings['payment_vat_display_only']) && $settings['payment_vat_display_only'] == 'on')) {
            $line_items['subtotal'] = array(
                'label' => wpbs_get_payment_default_string('subtotal', $language),
                'value' => wpbs_get_formatted_price($this->get_subtotal(), $this->get_display_currency(), true),
                'price' => $this->get_subtotal(),
                'class' => 'wpbs-line-item-subtotal wpbs-pricing-table-subtotal',
            );
        } else {
            unset($line_items['subtotal']);
        }

        return apply_filters('wpbs_get_pricing_table_line_items', $line_items, $this);
    }

    /**
     * Adds translations to the line items
     *
     * @param string $language
     * @param bool $unfiltered
     *
     * @return array
     *
     */
    public function get_localized_line_items($language, $unfiltered = false)
    {

        // Get line items
        $line_items = $this->get_line_items($unfiltered);

        // Add translation for "Total" label.
        $line_items['total']['label'] = wpbs_get_payment_default_string('total', $language);

        // Add translation for "Deposit" label.
        if (isset($line_items['first_payment'])) {
            $line_items['first_payment']['label'] = '&#8211; ' . wpbs_get_payment_default_string('part_payments_deposit', $language);
        }

        // Add translation for "Final Payment" label.
        if (isset($line_items['second_payment'])) {
            $line_items['second_payment']['label'] = '&#8211; ' . wpbs_get_payment_default_string('part_payments_final_payment_' . $this->get_final_payment_method(), $language);
        }

        return apply_filters('wpbs_get_localized_line_items', $line_items, $this);
    }

    /**
     * Get the multiplication field's value
     * 
     */
    public static function get_multiplication_field_value($field)
    {
        if (!isset($field['user_value'])) {
            return 0;
        }

        if (empty($field['user_value']) && strlen($field['user_value']) === 0) {
            return 0;
        }

        if (in_array($field['type'], array('product_dropdown', 'product_radio'))) {
            return (float) preg_replace("/[^0-9]/", "", explode('|', $field['user_value'])[1]);
        } elseif ($field['type'] == 'product_number') {
            return (float) $field['user_value'];
        } elseif (in_array($field['type'], array('product_checkbox', 'checkbox')) && !empty($field['user_value'])) {
            return 1;
        } else {
            return (float) $field['user_value'];
        }
    }

    /**
     * Get an "extra" line item's quantity string
     * 
     */
    public function get_extra_quantity_string($item)
    {

        $quantity_string = '';

        if (isset($item['extra_type']) && $item['extra_type'] == 'product_number') {
            $quantity_string .= ' &times; ' . $item['user_value'];
        }

        $quantity = isset($item['addition_multiplication']) && $item['addition'] == 'per_day' ? $item['addition_multiplication'] : $this->prices['quantity'];

        $quantity_string .= ' ' . ($item['addition'] == 'per_day' ? '&times; ' . ($item['multiplication'] !== false ? $quantity * $item['multiplication'] : $quantity) : ($item['multiplication'] !== false ? '&times; ' . $item['multiplication'] : ''));

        $quantity_string = trim($quantity_string);

        return $quantity_string;
    }

    /**
     * Get the daily price breakdown
     * 
     */
    public function get_price_breakdown()
    {
        $output = '<div class="wpbs-show-price-breakdown-toggle-wrap"><a href="#" class="wpbs-show-price-breakdown-toggle"><small>' . wpbs_get_payment_default_string('price_breakdown', $this->prices['language']) . '</small></a></div>';
        $output .= '<div class="wpbs-pricing-table-price-breakdown">';
        foreach ($this->prices['events']['individual_days'] as $day => $day_price) {
            $output .= '<span class="wpbs-pricing-table-price-breakdown-line"> &#8211; ';
            $day = DateTime::createFromFormat('Ymd', $day);
            $output .= wpbs_date_i18n(get_option('date_format'), $day->getTimestamp(), $this->prices['language']);
            $output .= '<span>' . wpbs_get_formatted_price($day_price, $this->prices['currency']) . '</span>';
            $output .= '</span>';
        }
        $output .= '</div>';
        return $output;
    }
}
