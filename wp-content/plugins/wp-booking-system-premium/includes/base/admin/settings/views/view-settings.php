<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$tabs 		= $this->get_tabs();
$active_tab = (! empty($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general');
$settings 	= get_option('wpbs_settings', array());

?>

<div class="wrap wpbs-wrap wpbs-wrap-settings">

	<?php wpbs_get_wp_admin_header(); ?>

	<!-- Plugin Sticky Header -->
	<div class="wpbs-plugin-sticky-header">

		<!-- Page Title -->
		<span class="wpbs-page-heading"><?php echo __('Settings', 'wp-booking-system'); ?></span>

		<!-- Buttons -->
		<div class="wpbs-header-buttons">

			<!-- Custom Save Button -->
			<a href="#" class="wpbs-primary-button wpbs-custom-save-button"><?php echo __('Save Changes', 'wp-booking-system'); ?></a>

		</div>

	</div>

	<!-- WP Notifications -->
	<div class="wpbs-notifications">
		<h1 class="wp-heading-inline"></h1>
	</div>


	<!-- Body -->
	<div class="wpbs-plugin-wrapper">
		<div class="wpbs-box">

			<!-- Tabs Nav -->
			<div class="wpbs-tabs-nav">
				<nav class="wpbs-tabs-menu">
					<ul>
						<?php

						if (! empty($tabs)) {
							foreach ($tabs as $tab_slug => $tab_name) {

								echo '<li><a href="' . add_query_arg(array('page' => 'wpbs-settings', 'tab' => $tab_slug), admin_url('admin.php')) . '" data-tab="' . $tab_slug . '" class="wpbs-nav-tab ' . ($active_tab == $tab_slug ? 'wpbs-nav-tab-active' : '') . '">' . $tab_name . '</a></li>';
							}
						}

						?>
					</ul>
				</nav>
			</div>


			<!-- Content -->
			<div class="wpbs-box-content">
				<form action="options.php" method="POST">

					<?php settings_fields('wpbs_settings'); ?>

					<!-- Tabs Contents -->
					<div class="wpbs-tab-wrapper">

						<?php

						if (! empty($tabs)) {

							foreach ($tabs as $tab_slug => $tab_name) {

								if (wpbs_enhanced_admin_ui() === false &&  $active_tab != $tab_slug) {
									continue;
								}


								echo '<div class="wpbs-tab wpbs-tab-' . $tab_slug . ' ' . ($active_tab == $tab_slug ? 'wpbs-active' : '') . '" data-tab="' . $tab_slug . '">';
									// Handle general tab
									if ($tab_slug == 'general') {

										include 'view-settings-tab-general.php';

										// Handle languages tab
									} else if ($tab_slug == 'languages') {

										include 'view-settings-tab-languages.php';

										// Handle form tabs
									} else if ($tab_slug == 'form') {

										include 'view-settings-tab-form.php';

										// Handle form tabs
									} else if ($tab_slug == 'email') {

										include 'view-settings-tab-email.php';

										// Handle strings tabs
									} else if ($tab_slug == 'strings') {

										include 'view-settings-tab-strings.php';

										// Handle dynamic tabs
									} else {

										/**
										 * Action to dynamically add content for each tab
										 *
										 */
										do_action('wpbs_submenu_page_settings_tab_' . $tab_slug);
									}

								echo '</div>';
							}
						}

						?>
					</div>

					<!-- Always update hidden -->
					<input type="hidden" name="wpbs_settings[always_update]" value="<?php echo (isset($settings['always_update']) && $settings['always_update'] == 1 ? 0 : 1); ?>" />

				</form>
			</div>


		</div>
	</div>

</div>