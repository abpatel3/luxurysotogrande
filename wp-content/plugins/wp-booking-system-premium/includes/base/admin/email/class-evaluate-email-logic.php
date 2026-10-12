<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The Email Logic Evaluation Class
 *
 */
class WPBS_Evaluate_Email_Logic
{
    /**
     * The email conditional logic rules
     *
     * @access protected
     * @var array
     *
     */
    protected $rules;

    /**
     * The payment data
     *
     * @access protected
     * @var WPBS_Payment
     *
     */
    protected $payment;

    /**
     * The booking data
     *
     * @access protected
     * @var WPBS_Booking
     *
     */
    protected $booking;

    /**
     * The pricing information
     *
     * @access protected
     * @var array
     *
     */
    protected $prices;

    /**
     * The form
     *
     * @access protected
     * @var WPBS_Form
     *
     */
    protected $form;

    /**
     * The form fields containing user data
     *
     * @access protected
     * @var array
     *
     */
    protected $form_fields;

    /**
     * The price per day
     *
     * @access protected
     * @var float
     *
     */
    protected $price_per_day;

    /**
     * The form subtotal
     *
     * @access protected
     * @var float
     *
     */
    protected $subtotal;

    /**
     * The calendar events price
     *
     * @access protected
     * @var float
     *
     */
    protected $events_price;

    /**
     * The form total
     *
     * @access protected
     * @var float
     *
     */
    protected $total;

    /**
     * The stay length
     *
     * @access protected
     * @var int
     *
     */
    protected $quantity;

    /**
     * The booking starting date
     *
     * @access public
     * @var string
     *
     */
    public $start_date;

    /**
     * The booking ending date
     *
     * @access public
     * @var string
     *
     */
    public $end_date;

    /**
     * The final evaluation of the email logic rules
     *
     * @access protected
     * @var bool
     *
     */
    protected $evaluation = false;

    /**
     * Evaluate against all rules or only a selection
     * 
     *  @access protected
     *  @var bool
     * 
     */
    protected $partial_evaluation;

    /**
     * @param WPBS_Email $email
     * @param WPBS_Booking $booking
     * @param bool $partial - Only evaluate some conditional rules, the ones that stay the same as the booking might change, eg. Payment Statuses. 
     * 
     */
    public function __construct($email, $booking, $partial_evaluation = false)
    {

        /**
         * Filter which evaluation rules to apply.
         * 
         * Eg. we cannot evaluate the payment status when the booking is made, because this might change. 
         * Same with the part payment statuses. 
         * 
         * This is used when scheduling emails, we still need to schedule emails even if not all 
         * evaluation rules are met. But there's no point in scheduling emails that will never be sent, 
         * that's why we only evaluate a few rules.
         * 
         */
        $this->partial_evaluation = $partial_evaluation;

        /**
         * Set the rules
         *
         */
        $this->rules = wpbs_get_email_meta($email->get('id'), 'notification_conditional_logic', true);

        /**
         * Set the start date
         *
         */
        $this->start_date = strtotime($booking->get('start_date'));

        /**
         * Set the end date
         *
         */
        $this->end_date = strtotime($booking->get('end_date'));

        /**
         * Set the booking
         *
         */
        $this->booking = $booking;

        /**
         * Set the pricing details
         *
         */
        $payment = wpbs_get_payment_by_booking_id($booking->get('id'));
        $this->payment = $payment;

        $this->prices = $payment ? $payment->get('prices') : false;

        /**
         * Set the quantity
         *
         */
        $this->quantity = ceil(($this->end_date - $this->start_date) / DAY_IN_SECONDS);
        if (wpbs_get_booking_meta($booking->get('id'), 'selection_style', true) == 'normal') {
            $this->quantity++;
        }

        if (wpbs_get_booking_meta($booking->get('id'), 'selection_style', true) == 'split' && $this->start_date == $this->end_date) {
            $this->quantity++;
        }

        /**
         * Set the subtotal
         *
         */
        $this->subtotal = $this->prices ? $this->prices['subtotal'] : 0;

        /**
         * Set the events price
         *
         */
        $this->events_price = $this->prices ? $this->prices['events']['price'] : 0;

        /**
         * Set the form
         *
         */
        $this->form = wpbs_get_form($booking->get('form_id'));

        /**
         * Set the form fields
         *
         */
        $this->form_fields = $booking->get('fields');

        /**
         * Calculate the average price per day
         * 
         */
        $this->price_per_day = $this->prices && $this->events_price ? $this->prices['events']['price'] / $this->quantity : 0; //todo $this->events_price

        /**
         * Evaluate all rule groups
         *
         */
        $this->evaluate_groups();

        /**
         * Evaluate email logic 
         *
         */
        $this->evaluate_email_logic();
    }

    /**
     * Returns the evaluation of the email_logic rules
     *
     * @return bool
     *
     */
    public function get_evaluation()
    {
        return $this->evaluation;
    }

    /**
     * Loop through all rule groups and rules and check if at least one group matches all rules
     *
     */
    protected function evaluate_email_logic()
    {

        // Loop through groups
        foreach ($this->rules as $group) {

            // Assume group is valid
            $group_valid = true;

            // Loop through rules
            foreach ($group as $rule) {

                // Check if rule is not valid, invalidate group as well.
                if ($rule['evaluation'] !== true) {
                    $group_valid = false;
                    break;
                }
            }

            // If at least one group is valid, set email logic evaluation to true
            if ($group_valid == true) {
                $this->evaluation = true;
                break;
            }
        }
    }

    /**
     * Evaluate rule groups
     *
     */
    protected function evaluate_groups()
    {
        foreach ($this->rules as &$group) {
            foreach ($group as &$rule) {
                $this->evaluate_rule($rule);
            }
        }
    }

    /**
     * Evaluate single rule
     *
     * @param array $rule
     *
     */
    protected function evaluate_rule(&$rule)
    {
        // Set operator
        $operator = $rule['comparison'];

        // Set compare value
        $compare = $rule['value'];

        // Switch conditions
        switch ($rule['condition']) {

            case 'total-order-value':
                $rule['evaluation'] = $this->evaluate($this->subtotal, $operator, $compare);
                break;

            case 'calendar-value':
                $rule['evaluation'] = $this->evaluate($this->events_price, $operator, $compare);
                break;

            case 'number-of-booked-days':
                $rule['evaluation'] = $this->evaluate($this->quantity, $operator, $compare);
                break;

            case 'price-per-day':
                if ($this->price_per_day === false) {
                    $rule['evaluation'] = false;
                    break;
                }
                $rule['evaluation'] = $this->evaluate($this->price_per_day, $operator, $compare);
                break;

            case 'user':
                $rule['evaluation'] = $this->evaluate(is_user_logged_in(), $operator, true);
                break;

            case 'payment_type':
                $rule['evaluation'] = $this->evaluate_payment_type($rule);
                break;

            case 'payment_method':
                $rule['evaluation'] = $this->evaluate_payment_method($rule);
                break;

            case 'booking_type':
                $rule['evaluation'] = $this->evaluate_booking_type($rule);
                break;

            case 'calendar_id':
                $rule['evaluation'] = $this->evaluate($this->booking->get('calendar_id'), $operator, absint($compare));
                break;

            case 'payment_status':
                if ($this->partial_evaluation) {
                    $rule['evaluation'] = true;
                    break;
                }
                $rule['evaluation'] = $this->evaluate_payment_status($rule);
                break;

            case 'user_role':
                $rule['evaluation'] = $this->evaluate_user_role($rule);
                break;

            case 'booking-weekday':
                $rule['evaluation'] = $this->evaluate_weekday($rule);
                break;

            case 'booking-day':
                $rule['evaluation'] = $this->evaluate_booking_day($rule);
                break;

            case 'booking-period':
                $today = new DateTime();
                $today->setTime(0, 0);

                $days_from_today = ($this->start_date - $today->getTimestamp()) / DAY_IN_SECONDS;
                $days_from_today = apply_filters('wpbs_email_logic_days_from_today_inclusion', $days_from_today, $this);

                $rule['evaluation'] = $this->evaluate($days_from_today, $operator, $compare);
                break;

            case 'form-field':
                $rule['evaluation'] = $this->evaluate_form_field($rule);
                break;

            case 'currency':
                $rule['evaluation'] = $this->evaluate_currency($rule);
                break;
        }
    }

    /**
     * Evaluate payment type
     *
     * @param array $rule
     *
     */
    protected function evaluate_payment_type($rule)
    {

        if (!$this->prices) {
            return false;
        }

        $part_payment = false;

        if (isset($this->prices['is_part_payment']) && $this->prices['is_part_payment'] == true && isset($this->prices['part_payments']['second_payment']) && $this->prices['part_payments']['second_payment'] > 0) {
            $part_payment = true;
        }

        if ($rule['comparison'] == 'deposit' && $part_payment == true) {
            return true;
        }

        if ($rule['comparison'] == 'full' && $part_payment == false) {
            return true;
        }

        return false;
    }

    /**
     * Evaluate payment method
     *
     * @param array $rule
     *
     */
    protected function evaluate_payment_method($rule)
    {

        if (!$this->prices) {
            return false;
        }

        if (!isset($this->prices['payment_method'])) {
            return false;
        }

        return $this->evaluate($this->prices['payment_method'], $rule['comparison'], $rule['value-payment_method']);
    }

    /**
     * Evaluate payment status
     *
     * @param array $rule
     *
     */
    protected function evaluate_payment_status($rule)
    {

        if (!$this->prices) {
            return false;
        }

        $payment_status = $this->get_payment_status();

        return $this->evaluate($payment_status, $rule['comparison'], $rule['value-payment_status']);
    }

    /**
     * Evaluate booking type
     *
     * @param array $rule
     *
     */
    protected function evaluate_booking_type($rule)
    {

        if (!$this->prices) {
            return false;
        }

        if (wpbs_get_booking_meta($this->payment->get('booking_id'), 'manual_booking', true)) {
            $booking_type = 'manual';
        } else {
            $booking_type = 'customer';
        }

        return $this->evaluate($booking_type, $rule['comparison'], $rule['value-booking_type']);
    }

    /**
     * Evaluate user role
     *
     * @param array $rule
     *
     */
    protected function evaluate_user_role($rule)
    {

        if (!is_user_logged_in()) {
            return false;
        }

        $user = wp_get_current_user();

        $roles = (array) $user->roles;

        $role = array_shift($roles);

        return $this->evaluate($role, $rule['comparison'], $rule['value-user-role']);
    }

    /**
     * Evaluate booking day
     *
     * @param array $rule
     *
     */
    protected function evaluate_booking_day($rule)
    {

        switch ($rule['comparison']) {
            case 'starts':
                $comparison = 'equal';
                $value = date('Y-m-d', $this->start_date);
                break;
            case 'ends':
                $comparison = 'equal';
                $value = date('Y-m-d', $this->end_date);
                break;
            case 'does-not-start':
                $comparison = 'not-equal';
                $value = date('Y-m-d', $this->start_date);
                break;
            case 'does-not-end':
                $comparison = 'not-equal';
                $value = date('Y-m-d', $this->end_date);
                break;
        }

        return $this->evaluate($value, $comparison, $rule['value-date']);
    }

    /**
     * Evaluate week
     *
     * @param array $rule
     *
     */
    protected function evaluate_weekday($rule)
    {

        switch ($rule['comparison']) {
            case 'starts':
                $comparison = 'equal';
                $value = date('N', $this->start_date);
                break;
            case 'ends':
                $comparison = 'equal';
                $value = date('N', $this->end_date);
                break;
            case 'does-not-start':
                $comparison = 'not-equal';
                $value = date('N', $this->start_date);
                break;
            case 'does-not-end':
                $comparison = 'not-equal';
                $value = date('N', $this->end_date);
                break;
        }

        return $this->evaluate($value, $comparison, $rule['value-weekday']);
    }

    /**
     * Evaluate form field
     *
     * @param array $rule
     *
     */
    protected function evaluate_form_field($rule)
    {
        if (empty($rule['form_field'])) {
            return false;
        }

        list($form_id, $form_field) = explode('-', $rule['form_field']);

        // Check if the field belogs to the current form
        if ($this->form->get('id') != $form_id) {
            return false;
        }

        $form_value = '';

        // Get current field value
        foreach ($this->form_fields as $field) {
            if ($field['id'] != $form_field) {
                continue;
            }

            // Get value
            $form_value = (isset($field['user_value'])) ? $field['user_value'] : '';

            // Handle Pricing options differently
            if (wpbs_form_field_is_product($field['type'])) {
                $form_value = wpbs_get_form_field_product_values($field);
            }

            if ($field['type'] == 'payment_method' && $form_value != '') {
                $form_value = wpbs_get_payment_methods()[$form_value];
            }
        }

        // Check if it's an array or not
        if (is_array($form_value)) {
            foreach ($form_value as $value) {
                if ($this->evaluate($value, $rule['comparison'], $rule['value']) == true) {
                    return true;
                }
            }
        } else {
            return $this->evaluate($form_value, $rule['comparison'], $rule['value']);
        }
    }

    /**
     * Evaluate the currency
     *
     * @param array $rule
     *
     */
    public function evaluate_currency($rule)
    {
        if (!$this->prices) {
            return false;
        }

        return $this->evaluate($this->prices['currency'], $rule['comparison'], $rule['value']);
    }

    /**
     * Helper function to compare two values with dynamic operator
     *
     * @param mixed $x
     * @param string $operator
     * @param mixed $y
     *
     * @return bool
     *
     */
    protected function evaluate($x, $operator, $y)
    {
        switch ($operator) {
            case 'lower':
                return ($x < $y);
            case 'greater':
                return ($x > $y);
            case 'equal':
                return ($x == $y);
            case 'not-equal':
                return ($x != $y);
            case 'logged_in':
                return ($x == $y);
            case 'not_logged_in':
                return ($x != $y);
            case 'contains':
                return stripos($x, $y) !== false;
            case 'not_contains':
                return stripos($x, $y) === false;
        }
        return false;
    }

    /**
     * Helper function to get the payment status of an order
     *
     * @return string
     *
     */
    protected function get_payment_status()
    {
        if (empty($this->payment)) {
            return false;
        }

        // Handle Payment on Arrival
        if ($this->payment->get('gateway') == 'payment_on_arrival' && $this->payment->is_paid()) {
            return 'paid';
        }

        // Error
        if ($this->payment->get('order_status') == 'error') {
            return 'error';
        }
        // Cancelled
        if ($this->payment->get('order_status') == 'cancelled') {
            return 'error';
        }

        // Delayed Capture
        if ($this->payment->get('order_status') == 'authorized') {
            if (strtotime($this->payment->get('date_created')) < current_time('timestamp') - (DAY_IN_SECONDS * 7)) {
                return 'error';
            }
            return 'authorized';
        }

        // Delayed Capture
        if ($this->payment->get('order_status') == 'pending') {
            return 'pending';
        }

        // Handle "Not Paid" bookings
        if (
            ($this->payment->is_part_payment() && !$this->payment->is_deposit_paid()) || // Part payments enabled but deposit wasn't paid
            ($this->payment->get('gateway') == 'bank_transfer' && !$this->payment->is_part_payment() && !$this->payment->is_paid()) // Bank transfer not paid
        ) {
            return 'not_paid';
        }

        // Handle Deposits
        if ($this->payment->is_part_payment() && $this->payment->is_deposit_paid() && !$this->payment->is_final_payment_paid()) { // Only Deposit was paid
            if (wpbs_get_booking_meta($this->payment->get('booking_id'), 'manual_booking', 'true') && $this->payment->get('gateway') != 'bank_transfer') {
                return 'not_paid';
            }
            return 'deposit_paid';
        }

        // If we got so far, it means the booking was paid for.
        return 'paid';
    }
}
