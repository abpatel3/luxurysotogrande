<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

?>

<div id="wpbs-modal-add-calendar-shortcode" class="wpbs-modal">
	<div class="wpbs-plugin-wrapper">

		<div class="wpbs-box">
			<!-- Modal Header -->
			<div class="wpbs-box-header">

				<h1>
					<span class="dashicons dashicons-calendar-alt"><!-- --></span>
					<?php echo __('Add Calendar', 'wp-booking-system'); ?>
				</h1>

				<span class="wpbs-modal-close dashicons dashicons-no-alt"><!-- --></span>

			</div>

			<!-- Modal Body -->
			<div class="wpbs-modal-body">

				<!-- Tabs Nav -->
				<div class="wpbs-tabs-nav">
					<nav class="wpbs-tabs-menu">
						<ul>
							<?php

							if (! empty($tabs)) {
								foreach ($tabs as $tab_slug => $tab_name) {
									echo '<li><a class="wpbs-nav-tab wpbs-modal-nav-tab ' . ('insert-calendar' == $tab_slug ? 'wpbs-nav-tab-active' : '') . '" data-tab="' . $tab_slug . '" href="#">' . $tab_name . '</a></li>';
								}
							}

							?>

						</ul>
					</nav>
				</div>

				<!-- Modal Body Inner -->
				<div class="wpbs-modal-inner">

					<!-- Tabs Contents -->
					<div class="wpbs-tab-wrapper">
						<?php
						if (! empty($tabs)) {
							$dir_path = plugin_dir_path(__FILE__);

							$calendars = wpbs_get_calendars(array('status' => 'active'));

							foreach ($tabs as $tab_slug => $tab_name) {
								if (file_exists($dir_path . 'view-shortcode-generator-' . $tab_slug . '.php')) {
									include 'view-shortcode-generator-' . $tab_slug . '.php';
								} else {
									do_action('wpbs_shortcode_generator_tab_' . $tab_slug);
								}
							}
						}
						?>


					</div>
				</div>

			</div>
		</div>

	</div>
</div>

<div id="wpbs-modal-add-calendar-shortcode-overlay" class="wpbs-modal-overlay"><!-- --></div>