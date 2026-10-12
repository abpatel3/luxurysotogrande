<?php $logs = wpbs_get_booking_meta($this->booking->get('id'), 'email_log');  ?>

<?php if (strpos($_SERVER['HTTP_REFERER'], 'wpbs-message=email-resent') !== false): ?>
    <div class="wpbs-page-notice notice-updated">
        <p><?php echo __('Email successfully resent.', 'wp-booking-system') ?></p>
    </div>
<?php endif; ?>

<table>
    <tr>
        <th><?php echo __('Send Date', 'wp-booking-system') ?></th>
        <th><?php echo __('Email Type', 'wp-booking-system') ?></th>
        <th><?php echo __('Recipient', 'wp-booking-system') ?></th>
        <th class="email-log-table-message"><?php echo __('Message', 'wp-booking-system') ?></th>
        <th class="email-log-table-actions"><?php echo __('Actions', 'wp-booking-system') ?></th>
    </tr>

    <?php if (count($logs) > 0): ?>
        <?php foreach ($logs as $meta_id => $log): ?>

            <tr>
                <td>
                    <?php echo wpbs_date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $log['send_date']) ?>
                </td>
                <td>
                    <?php
                    $email_type_from = array('user', 'customer', 'accept_booking', 'reminder', 'followup', 'payment_success', 'payment');
                    $email_type_to = array(
                        __('User Notification', 'wp-booking-system'),
                        __('Booking Custom Email', 'wp-booking-system'),
                        __('Booking Accept/Delete Email', 'wp-booking-system'),
                        __('Classic Reminder', 'wp-booking-system'),
                        __('Follow Up', 'wp-booking-system'),
                        __('Payment Success Notification', 'wp-booking-system'),
                        __('Payment Reminder', 'wp-booking-system'),
                    );
                    echo str_replace($email_type_from, $email_type_to, $log['email_type']);
                    if (in_array($log['email_type'], ['customer', 'accept_booking'])) {
                        echo '<br><small><strong>Subject:</strong> ' . $log['subject'] . '</small>';
                    }

                    ?>
                </td>
                <td>
                    <?php echo $log['send_to'] ?>
                    <?php if(isset($log['send_to_cc']) && $log['send_to_cc']): ?>
                        <br><small><strong>CC:</strong> <?php echo trim($log['send_to_cc'], ', '); ?></small>
                    <?php endif; ?>
                    <?php if(isset($log['send_to_bcc']) && $log['send_to_bcc']): ?>
                        <br><small><strong>BCC:</strong> <?php echo trim($log['send_to_bcc'], ', '); ?></small>
                    <?php endif; ?>
                </td>

                <td class="email-log-table-message">
                    <a target="_blank" href="<?php echo add_query_arg(array('page' => 'wpbs-calendars', 'wpbs_action' => 'email_logs', 'booking_id' => $this->booking->get('id'), 'email_log_id' => $meta_id, 'noheader' => 'true'), admin_url('admin.php')); ?>"><?php echo __('View email body', 'wp-booking-system') ?></a>
                </td>

                <td class="email-log-table-actions">
                    <a onclick="return confirm('<?php echo __('Are you sure you want to resend this email? Please note that attachments will not be included.', 'wp-booking-system'); ?>' )" href="<?php echo add_query_arg(array('page' => 'wpbs-calendars', 'wpbs_action' => 'resend_email', 'booking_id' => $this->booking->get('id'), 'calendar_id' => $this->booking->get('calendar_id'), 'email_log_id' => $meta_id), admin_url('admin.php')); ?>"><?php echo __('Resend email', 'wp-booking-system') ?></a>
                </td>
            </tr>


        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4"><?php echo __('No emails sent yet.', 'wp-booking-system') ?></td>
        </tr>
    <?php endif; ?>
</table>