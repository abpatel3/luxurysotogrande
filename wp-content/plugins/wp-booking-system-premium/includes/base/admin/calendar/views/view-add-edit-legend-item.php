<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$page_heading       = ($_GET['subpage'] == 'add-legend-item' ? __('Add New Legend Item', 'wp-booking-system') : __('Edit Legend Item', 'wp-booking-system'));
$page_wrap_class    = ($_GET['subpage'] == 'add-legend-item' ? 'wpbs-wrap-add-legend-item' : 'wpbs-wrap-edit-legend-item');
$submit_button_text = ($_GET['subpage'] == 'add-legend-item' ? __('Add Legend Item', 'wp-booking-system') : __('Save Legend Item', 'wp-booking-system'));
$action				= ($_GET['subpage'] == 'add-legend-item' ? 'add_legend_item' : 'edit_legend_item');

$name  		= (! empty($_POST['legend_item_name']) ? $_POST['legend_item_name'] : '');
$type  		= (! empty($_POST['legend_item_type']) ? $_POST['legend_item_type'] : '');
$color 		= (! empty($_POST['legend_item_color']) ? $_POST['legend_item_color'] : array());
$color_text = (! empty($_POST['legend_item_color_text']) ? $_POST['legend_item_color_text'] : '');

$settings = get_option('wpbs_settings', array());

// Set values for the edit page
if ($_GET['subpage'] == 'edit-legend-item') {

	$legend_item = wpbs_get_legend_item(absint($_GET['legend_item_id']));

	$name  		= (! empty($name) ? $name : $legend_item->get('name'));
	$type  		= (! empty($type) ? $type : $legend_item->get('type'));
	$color 		= (! empty($color) ? $color : $legend_item->get('color'));
	$color_text = (! empty($color_text) ? $color_text : $legend_item->get('color_text'));
}

?>

<div class="wrap wpbs-wrap <?php echo $page_wrap_class; ?>">

	<?php wpbs_get_wp_admin_header(); ?>

	<form action="" method="POST">
		<!-- Plugin Sticky Header -->
		<div class="wpbs-plugin-sticky-header">

			<!-- Page Title -->
			<span class="wpbs-page-heading"><?php echo $page_heading; ?></span>

			<!-- Buttons -->
			<div class="wpbs-header-buttons">
				<a class="wpbs-secondary-button" href="<?php echo add_query_arg(array('subpage' => 'view-legend'), remove_query_arg(array('legend_item_id', 'wpbs_message'))); ?>"><?php echo __('Back to Legend', 'wp-booking-system'); ?></a>
				<input type="submit" value="<?php echo $submit_button_text; ?>" class="wpbs-primary-button" />
			</div>

		</div>

		<!-- WP Notifications -->
		<div class="wpbs-notifications">
			<h1 class="wp-heading-inline"></h1>
		</div>



		<!-- Body -->
		<div class="wpbs-plugin-wrapper">
			<div class="wpbs-box">

				<div class="wpbs-box-header">
					<h2><?php echo __('Legend Options', 'wp-booking-system'); ?></h2>
				</div>

				<div class="wpbs-box-content">
					<div class="wpbs-box-content-inner">

						<!-- Name -->
						<div class="wpbs-form-field">
							<label class="wpbs-label" for="wpbs-legend-item-name"><?php echo __('Name', 'wp-booking-system'); ?></label>
							<div class="wpbs-input">
								<input id="wpbs-legend-item-name" name="legend_item_name" type="text" value="<?php echo esc_attr($name); ?>" class="wpbs-medium" />
							</div>
						</div>

						<!-- Type -->
						<div class="wpbs-form-field">
							<label class="wpbs-label" for="wpbs-legend-item-type"><?php echo __('Type', 'wp-booking-system'); ?></label>
							<div class="wpbs-input">
								<select id="wpbs-legend-item-type" name="legend_item_type" class="wpbs-medium">
									<option value="single" <?php selected($type, 'single'); ?>><?php echo __('Single', 'wp-booking-system'); ?></option>
									<option value="split" <?php selected($type, 'split'); ?>><?php echo __('Split', 'wp-booking-system'); ?></option>
								</select>
							</div>
						</div>

						<!-- Color -->
						<div class="wpbs-form-field">
							<label class="wpbs-label" for="color-1"><?php echo __('Color', 'wp-booking-system'); ?></label>

							<div class="wpbs-input">
								<input id="wpbs-legend-item-color-1" name="legend_item_color[]" class="wpbs-colorpicker" type="text" value="<?php echo esc_attr(! empty($color[0]) ? $color[0] : ''); ?>" class="wpbs-medium" />
								<input id="wpbs-legend-item-color-2" name="legend_item_color[]" class="wpbs-colorpicker" type="text" value="<?php echo esc_attr(! empty($color[1]) ? $color[1] : ''); ?>" class="wpbs-medium" />
							</div>
						</div>

						<!-- Text Color -->
						<div class="wpbs-form-field">
							<label class="wpbs-label" for="color-text"><?php echo __('Text Color', 'wp-booking-system'); ?></label>

							<div class="wpbs-input">
								<input id="wpbs-legend-item-color-text" name="legend_item_color_text" class="wpbs-colorpicker" type="text" value="<?php echo esc_attr(! empty($color_text) ? $color_text : ''); ?>" class="wpbs-medium" />
							</div>
						</div>



						<!-- Translations Panel -->
						<?php
						$active_languages = (! empty($settings['active_languages']) ? $settings['active_languages'] : array());
						$languages 		  = wpbs_get_languages();
						?>
						<?php if (! empty($active_languages)): ?>

							<div class="wpbs-divider"></div>
							<h2><?php echo __('Translations', 'wp-booking-system'); ?></h2>


							<?php foreach ($active_languages as $code): ?>

								<?php

								if ($_GET['subpage'] == 'edit-legend-item')
									$value = wpbs_get_legend_item_meta($legend_item->get('id'), 'translation_' . $code, true);
								else
									$value = '';

								?>

								<div class="wpbs-form-field wpbs-small-margin">
									<div class="wpbs-input">
										<div class="wpbs-input-wrapper">
											<span class="wpbs-input-symbol wpbs-input-symbol-left wpbs-flag-symbol">
												<span><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $code; ?>.png" /> <?php echo strtoupper($code); ?></span>
											</span>
											<input id="wpbs-legend-item-translation-<?php echo esc_attr($code); ?>" name="legend_item_translation_<?php echo esc_attr($code); ?>" type="text" value="<?php echo esc_attr($value); ?>" class="wpbs-medium" />
										</div>
									</div>
								</div>
							<?php endforeach; ?>

						<?php endif; ?>
					</div>

					<!-- Save Page Button -->
					<div class="wpbs-box-footer-wrapper">
						<div class="wpbs-box-footer">
							<input type="submit" class="wpbs-primary-button" value="<?php echo $submit_button_text; ?>" />
						</div>
					</div>

				</div>
			</div><!-- / Settings Panel -->

			<!-- Extra -->
			<?php do_action('wpbs_admin_view_add_edit_legend_item'); ?>

			<!-- Nonce -->
			<?php wp_nonce_field('wpbs_' . $action, 'wpbs_token', false); ?>
			<input type="hidden" name="wpbs_action" value="<?php echo $action; ?>" />





		</div>
	</form>

</div>