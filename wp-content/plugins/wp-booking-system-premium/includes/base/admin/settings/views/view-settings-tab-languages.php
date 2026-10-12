<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

?>

<div class="wpbs-tab-inner">

	<h2><?php echo __('Languages', 'wp-booking-system'); ?></h2>

	<div class="wpbs-form-field">

		<label class="wpbs-label">
			<?php echo __('What languages do you wish to use?', 'wp-booking-system'); ?>
		</label>

		<div class="wpbs-input wpbs-has-columns">

			<?php

			$languages = wpbs_get_languages();

			foreach ($languages as $code => $name) {
				echo '<div class="wpbs-pill-checkbox wpbs-third">';
				echo '<input type="checkbox" id="language-' . esc_attr($code) . '" name="wpbs_settings[active_languages][]" value="' . esc_attr($code) . '" ' . (! empty($settings['active_languages']) && in_array($code, $settings['active_languages']) ? 'checked' : '') . ' />';
				echo '<label for="language-' . esc_attr($code) . '">';
				echo '<span class="flag"><img src="' . WPBS_PLUGIN_DIR_URL . 'assets/img/flags/' . esc_attr($code) . '.png" /></span>';
				echo esc_html($name);
				echo '</label>';
				echo '</div>';
			}

			?>

		</div>

	</div>
</div>

<!-- Save Page Button -->
<div class="wpbs-box-footer-wrapper">
	<div class="wpbs-box-footer">
		<input type="submit" class="wpbs-primary-button" value="<?php echo __('Save Settings', 'wp-booking-system'); ?>" />
	</div>
</div>