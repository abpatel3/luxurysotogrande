<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The main class for the Payment
 *
 */
class WPBS_VAT
{

    private $plugin_settings;

    private $enabled;

    private $percentage;

    private $vat_amount = 0;

    private $vat_amount_breakdown = [];

    public $percentage_calculation = 1;


    public function __construct($form_id = false, $post_data = false)
    {

        $this->plugin_settings = get_option('wpbs_settings');

        $this->enabled = $this->check_if_enabled();

        if ($this->enabled === false) {
            return false;
        }

        $this->percentage =  floatval($this->plugin_settings['payment_vat_percentage']);

        if($form_id !== false) {
            $form_vat = wpbs_get_form_meta($form_id, 'custom_vat_percentage', true);
            if($form_vat) {
                $this->percentage = floatval($form_vat);
            }
        }

        $this->percentage = apply_filters('wpbs_vat_percentage', $this->percentage, $post_data);

        $this->percentage_calculation = 1 + $this->percentage / 100;
    }

    public function check_if_enabled()
    {
        if (!isset($this->plugin_settings['payment_vat_enable']) || $this->plugin_settings['payment_vat_enable'] != 'on') {
            return false;
        }

        if (!isset($this->plugin_settings['payment_vat_percentage'])) {
            return false;
        }

        if (absint($this->plugin_settings['payment_vat_percentage']) == 0) {
            return false;
        }

        return true;
    }

    public function is_enabled()
    {
        return $this->enabled;
    }

    public function is_display_only()
    {
        return isset($this->plugin_settings['payment_vat_display_only']) && $this->plugin_settings['payment_vat_display_only'] == 'on';
    }

    public function deduct_vat($price, $add = true, $custom_vat_rate = false)
    {
        if (!$this->is_enabled()) {
            return $price;
        }

        $vat_rate = $custom_vat_rate !== false ? (1 + $custom_vat_rate / 100) : $this->percentage_calculation;

        $price_without_vat = $price / $vat_rate;

        if ($add) {
            $this->add_vat_amount($price - $price_without_vat, $vat_rate);
        }

        if ($this->is_display_only()) {
            return $price;
        }

        return round($price_without_vat, 2);
    }

    public function add_vat_amount($amount, $rate = false)
    {
        if ($rate === false) {
            $rate = $this->percentage_calculation;
        }
        // Add the default rate
        $this->vat_amount += round($amount, 2);

        // Add custom rates
        $rate = round(($rate * 10000) - 10000);

        if (!isset($this->vat_amount_breakdown[$rate])) {
            $this->vat_amount_breakdown[$rate] = 0;
        }
        $this->vat_amount_breakdown[$rate] += round($amount, 2);
    }

    public function get_vat_amount()
    {
        return $this->vat_amount;
    }

    public function get_vat_amount_breakdown()
    {
        return $this->vat_amount_breakdown;
    }

    public function get_name($language)
    {
        if (isset($this->plugin_settings['payment_vat_name_translation_' . $language]) && !empty($this->plugin_settings['payment_vat_name_translation_' . $language])) {
            return $this->plugin_settings['payment_vat_name_translation_' . $language];
        }

        if (isset($this->plugin_settings['payment_vat_name']) && !empty($this->plugin_settings['payment_vat_name'])) {
            return $this->plugin_settings['payment_vat_name'];
        }

        return __('VAT', 'wp-booking-system');
    }

    public function get_percentage()
    {
        return $this->percentage;
    }
}
