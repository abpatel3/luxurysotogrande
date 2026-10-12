
<div class="wpbs-tab-inner">

	<h2><?php _e('Regenerate Email Notification Cron Jobs', 'wp-booking-system') ?></h2>

	<div class="wpbs-page-notice notice-error">

		<p><?php echo __('This will remove all existing cron jobs and rebuild them based on current email settings.', 'wp-booking-system'); ?></p>

	</div>

	<a class="wpbs-secondary-button wpbs-remove-button" onclick="return confirm('<?php _e('Are you sure you want to proceed?', 'wp-booking-system'); ?>');" href="<?php echo add_query_arg(array('wpbs_action' => 'regenerate_scheduled_email_cron_jobs', 'wpbs_token' => wp_create_nonce('wpbs_regenerate_scheduled_email_cron_jobs')), admin_url('admin.php')); ?>"><i class="wpbs-icon-trash"></i> <?php echo __('Regenerate Email Notification Cron Jobs', 'wp-booking-system'); ?></a>

	<?php

	/**
	 * Hook to add extra fields at the bottom of the Tools Tab
	 *
	 * @param array $settings
	 *
	 */
	do_action('wpbs_submenu_page_settings_tab_tools_cron');

	?>


</div>