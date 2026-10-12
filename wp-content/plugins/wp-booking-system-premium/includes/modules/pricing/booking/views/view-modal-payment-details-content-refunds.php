<?php $payment_details = $payment->get('details'); ?>

<?php if(isset($payment_details['refunds'])): ?>
    <?php foreach($payment_details['refunds'] as $i => $refund): ?>
    <tr class="wpbs-booking-details-refund-row wpbs-booking-details-refunded-line">
        <td><strong><?php echo __('Refund #', 'wp-booking-system') ?><?php echo $i+1; ?></strong></td>
        <td>
            <p><strong><?php echo wpbs_get_formatted_price($refund['amount'], ($payment->get_currency())) ?></strong> <?php echo __('refunded on', 'wp-booking-system') ?> <strong><?php echo wpbs_date_i18n(get_option('date_format') . ', ' . get_option('time_format'), $refund['date']) ?></strong></p>
            <p>
                <?php if($refund['reason']): ?>
                    <small><strong><?php echo __('Reason', 'wp-booking-system');?></strong>: <?php echo $refund['reason'] ?></small>
                <?php endif; ?>
                <small><strong><?php echo __('Type', 'wp-booking-system');?></strong>: <?php echo str_replace(['manual', 'automatic'], [__('Manual Refund', 'wp-booking-systme'), __('Automatic Refund', 'wp-booking-systme')], $refund['type']) ?></small>
                
            </p>
        </td>
    </tr>
    <?php endforeach; ?>
<?php endif; ?>

<?php if(wpbs_payment_gateway_supports_refunds($payment) == true && $payment->get_refund_status() != 'fully_refunded'): ?>

        <tr class="wpbs-booking-details-refund-row wpbs-booking-details-refunds">
            <td><strong><?php echo __('Refund', 'wp-booking-system') ?>:</strong></td>
            <td>
                <a href="#" class="wpbs-secondary-button wpbs-booking-details-refund-form-open"><?php echo __('Refund', 'wp-booking-system') ?></a>

                <span class="wpbs-booking-details-refund-form">
                    
                    <?php $charges = wpbs_payment_get_charges($payment); if($charges): ?>

                        <?php  foreach($charges as $charge): ?>

                            <div class="wpbs-booking-details-refund-charge wpbs-form-fields-indent wpbs-form-fields-repeater">

                                <h3 class="hndle">
                                    <span><?php echo $charge['name'] ?></span>
                                    <small>(<?php echo sprintf(__('Original charge was for %s', 'wp-booking-system'), wpbs_get_formatted_price($charge['amount'], ($payment->get_currency()))) ?>)</small>
                                </h3>
                                

                                <span class="wpbs-booking-details-refund-error"></span>

                                <?php if($charge['available'] > 0): ?>

                                    <div class="wpbs-form-field wpbs-small-margin">
                                        <label class="wpbs-label" for="wpbs-refund-amount"><?php _e('Amount', 'wp-booking-system') ?></label>
                                        <div class="wpbs-input">
                                            <div class="wpbs-input-wrapper">
                                                <input name="wpbs-refund-amount" class="wpbs-refund-amount" type="number" min="1" max="<?php echo $charge['available'];?>" value="<?php echo $charge['available'];?>">
                                                <span class="wpbs-input-symbol wpbs-input-symbol-right"><span><?php echo $payment->get_currency(); ?></span></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="wpbs-form-field wpbs-small-margin">
                                        <label class="wpbs-label" for="wpbs-refund-reason"><?php _e('Reason', 'wp-booking-system') ?></label>
                                        <div class="wpbs-input">
                                            <input type="text" name="wpbs-refund-reason" class="wpbs-refund-reason" placeholder="<?php _e('eg. customer cancelled, discount...', 'wp-booking-system') ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="wpbs-form-field wpbs-small-margin">
                                        <button class="wpbs-primary-button wpbs-refund-process" data-booking-id="<?php echo $payment->get('booking_id');?>" data-payment-gateway="<?php echo $payment->get_payment_gateway();?>" data-charge-id="<?php echo $charge['id'];?>"><?php _e('Refund', 'wp-booking-system') ?></button>
                                    </div>
                                <?php else: ?>
                                    <p><?php _e('Fully refunded.', 'wp-booking-system') ?></p>
                                <?php endif; ?>
                                
                            </div>
                            
                        <?php endforeach; ?>

                    <?php else: ?>

                        <p><?php echo __('No refundable charges.', 'wp-booking-system') ?></p>

                    <?php endif; ?>

                    <button class="wpbs-secondary-button" id="wpbs-refund-cancel"><?php _e('Cancel', 'wp-booking-system') ?></button>

                </span>
            </td>
        </tr>
    

<?php endif; ?>