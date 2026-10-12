<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$serial_key = get_option('wpbs_serial_key', '');
$website_id = get_option('wpbs_registered_website_id', '');

?>

<div class="wpbs-tab-inner">

	<!-- Serial Key -->
	<div id="wpbs-wpbs-settings-field-register-website" class="wpbs-form-field">

		<label class="wpbs-label"><?php echo __('Serial Key', 'wp-booking-system'); ?></label>

		<div class="wpbs-input">

			<input type="text" name="serial_key" <?php echo (! empty($serial_key) ? 'disabled' : ''); ?> value="<?php echo $serial_key ? (str_pad(substr($serial_key, 0, 11), 77, '&bull;')) : ''; ?>" />

			<?php if (empty($website_id)): ?>
				<a id="wpbs-register-website-button" class="wpbs-primary-button" href="<?php echo add_query_arg(array('tab' => 'register_website', 'wpbs_action' => 'register_website', 'wpbs_token' => wp_create_nonce('wpbs_register_website')), admin_url('admin.php')); ?>"><?php echo __('Register Website', 'wp-booking-system'); ?></a>
			<?php else: ?>
				<a id="wpbs-deregister-website-button" class="wpbs-secondary-button" href="<?php echo add_query_arg(array('tab' => 'register_website', 'wpbs_action' => 'deregister_website', 'wpbs_token' => wp_create_nonce('wpbs_deregister_website')), admin_url('admin.php')); ?>"><?php echo __('Deregister Website', 'wp-booking-system'); ?></a>
			<?php endif; ?>

		</div>

		<div class="wpbs-label-description"><?php echo  sprintf(__('You can find your serial key in your WP Booking System account. %sClick here to go to your account.%s', 'wp-booking-system'), '<a href="https://www.wpbookingsystem.com/account/" target="_blank">', '</a>'); ?></div>

	</div>

	<!-- Check for Updates -->
	<div id="wpbs-wpbs-settings-field-register-website" class="wpbs-form-field">

		<label class="wpbs-label"><?php echo __('Manual Update Check', 'wp-booking-system'); ?></label>

		<div class="wpbs-input">

			<a id="wpbs-check-for-updates-button" class="wpbs-secondary-button" <?php echo (empty($serial_key) || empty($website_id) ? 'disabled' : ''); ?> href="<?php echo add_query_arg(array('tab' => 'register_website', 'wpbs_action' => 'check_for_updates', 'wpbs_token' => wp_create_nonce('wpbs_check_for_updates')), admin_url('admin.php')); ?>"><?php echo __('Check for Updates', 'wp-booking-system'); ?></a>

		</div>

		<div class="wpbs-label-description"><?php echo  sprintf(__('The plugin by default checks once a day if there is an update available for it and displays if there is one in the %1$sPlugins page%2$s. If you want to do a manual update check and not wait for the automatic one, press the Check for Updates button and then check the %1$sPlugins page%2$s.', 'wp-booking-system'), '<a href="' . admin_url('plugins.php') . '">', '</a>'); ?></div>

	</div>

</div>