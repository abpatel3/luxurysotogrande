<?php
$form_id   = absint(! empty($_GET['form_id']) ? $_GET['form_id'] : 0);
$form      = wpbs_get_form($form_id);

if (is_null($form))
    return;

$form_meta = wpbs_get_form_meta($form_id);
$form_data = $form->get('fields');
?>

<div class="wpbs-tab-inner">

    <h2><?php echo __('Bookings Manager Field Mappings', 'wp-booking-system-booking-manager'); ?></h2>

    <!-- Form Changed Notice -->
    <div class="wpbs-page-notice notice-info wpbs-form-changed-notice">
        <p><?php echo __('It appears you made changes to the form. Make sure you save the form before you make any changes on this page to ensure all fields are up to date.', 'wp-booking-system-invoices'); ?></p>
    </div>

    <!-- Fields -->
    <div class="wpbs-form-field">

        <label class="wpbs-label" for="booking_manager_fields">
            <?php echo __('Fields', 'wp-booking-system-booking-manager'); ?>
        </label>

        <?php $selected_fields = isset($form_meta['booking_manager_fields'][0]) ? (is_array($form_meta['booking_manager_fields'][0]) ? $form_meta['booking_manager_fields'][0] : unserialize($form_meta['booking_manager_fields'][0])) : array(); ?>
        <div class="wpbs-input wpbs-chosen-wrapper">
            <input type="hidden" name="booking_manager_fields" value="" />
            <select name="booking_manager_fields[]" class="wpbs-chosen" id="booking_manager_fields" multiple>
                <optgroup label="<?php _e('Form Fields', 'wp-booking-system-booking-manager');?>">
                <?php foreach ($form_data as $field): if (in_array($field['type'], wpbs_get_excluded_fields(array('hidden')))) {
                        continue;
                    }  ?>
                    <option value="<?php echo $field['id']; ?>" <?php echo (in_array($field['id'], $selected_fields)) ? 'selected' : ''; ?>><?php echo isset($field['values']['default']['label']) ? $field['values']['default']['label'] : '- (' . str_replace('_', ' ', $field['type']) . ' field)' ?></option>
                <?php endforeach; ?>
                </optgroup>
                <optgroup label="<?php _e('Pricing Data', 'wp-booking-system-booking-manager');?>">
                    <option <?php echo (in_array('total_amount', $selected_fields)) ? 'selected' : ''; ?> value="total_amount"><?php _e('Booking Total Value', 'wp-booking-system-booking-manager');?></option>
                    <option <?php echo (in_array('deposit_amount', $selected_fields)) ? 'selected' : ''; ?> value="deposit_amount"><?php _e('Deposit Amount', 'wp-booking-system-booking-manager');?></option>
                    <option <?php echo (in_array('final_payment_amount', $selected_fields)) ? 'selected' : ''; ?> value="final_payment_amount"><?php _e('Final Payment Amount', 'wp-booking-system-booking-manager');?></option>
                </optgroup>
            </select>
        </div>

        <div class="wpbs-label-description"><?php echo __("Select which fields will appear in the Bookings List page for bookings made with this form.", 'wp-booking-system-booking-manager'); ?></div>

    </div>
</div>