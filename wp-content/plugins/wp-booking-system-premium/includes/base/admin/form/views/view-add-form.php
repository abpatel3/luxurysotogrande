<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;
?>

<div class="wrap wpbs-wrap wpbs-wrap-add-form">

	<?php wpbs_get_wp_admin_header(); ?>

	<!-- Plugin Sticky Header -->
	<div class="wpbs-plugin-sticky-header">

		<!-- Page Title -->
		<span class="wpbs-page-heading"><?php echo __('Add New Form', 'wp-booking-system'); ?></span>

		<!-- Buttons -->
		<div class="wpbs-header-buttons">
			<a href="<?php echo add_query_arg(array('page' => 'wpbs-forms'), admin_url('admin.php')); ?>" class="wpbs-secondary-button"><?php echo __('Back to all forms', 'wp-booking-system') ?></a>
		</div>

	</div>

	<!-- WP Notifications -->
	<div class="wpbs-notifications">
		<h1 class="wp-heading-inline"></h1>
	</div>

	<div class="wpbs-plugin-wrapper">
		<div class="wpbs-box wpbs-box-half wpbs-box-centered">
			<form action="" method="POST">

				<div class="wpbs-box-header">
					<h2>
						<div class="wpbs-add-icon">
							<span class="dashicons dashicons-calendar-alt"></span>
						</div>
						<?php echo __('Add New Form', 'wp-booking-system'); ?>
					</h2>
				</div>

				<div class="wpbs-box-content">

					<div class="wpbs-form-field">
						<label class="wpbs-label" for="wpbs-new-form-name"><?php echo __('Form Name', 'wp-booking-system'); ?></label>
						<div class="wpbs-input">
							<input id="wpbs-new-form-name" name="form_name" type="text" value="<?php echo (! empty($_POST['form_name']) ? esc_attr($_POST['form_name']) : ''); ?>" />
						</div>
					</div>

				</div>

				<div class="wpbs-box-footer-wrapper">
					<div class="wpbs-box-footer">
						<div class="wpbs-spaced-buttons">
							<input type="submit" class="wpbs-primary-button" value="<?php echo __('Add Form', 'wp-booking-system'); ?>" />
							<a class="wpbs-secondary-button wpbs-remove-button" href="<?php echo admin_url($this->admin_url); ?>"><?php echo __('Cancel', 'wp-booking-system'); ?></a>
						</div>

						<!-- Action and nonce -->
						<input type="hidden" name="wpbs_action" value="add_form" />
						<?php wp_nonce_field('wpbs_add_form', 'wpbs_token', false); ?>

					</div>
				</div>

			</form>

		</div>
	</div>

</div>
