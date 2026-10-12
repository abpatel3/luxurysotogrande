<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$tools_tabs = wpbs_get_tools_tabs();
$active_section = (isset($_GET['section']) && ! empty($_GET['section'] && array_key_exists($_GET['section'], $tools_tabs)) ? sanitize_text_field($_GET['section']) : 'data-deletion');
?>

<ul class="wpbs-sub-tab-navigation">
	<?php
	if (! empty($tools_tabs)) {
		$i = 0;
		foreach ($tools_tabs as $tab_slug => $tab_name) {

			echo '<li><a href="' . add_query_arg(array('page' => 'wpbs-settings', 'tab' => 'strings', 'section' => $tab_slug), admin_url('admin.php')) . '" data-tab="' . $tab_slug . '" ' . ($active_section == $tab_slug  ? ' class="current"' : '') . '>' . $tab_name . '</a></li>';
			$i++;
		}
	}
	?>
</ul>

<div class="wpbs-clear"><!-- --></div>

<div class="wpbs-sub-tabs">

	<?php

	if (! empty($tools_tabs)) {

		foreach ($tools_tabs as $tab_slug => $tab_name) {

			echo '<div class="wpbs-tab wpbs-tab-' . $tab_slug . ($active_section == $tab_slug ? ' wpbs-section-active' : '') . ' " data-tab="' . $tab_slug . '">';
			// Handle general tab
			if ($tab_slug == 'data-deletion') {

				include 'view-tools-tab-data-deletion.php';

				// Handle dynamic tabs
			} elseif ($tab_slug == 'calendar') {

				include 'view-tools-tab-calendar.php';

				// Handle dynamic tabs
			} elseif ($tab_slug == 'cron') {

				include 'view-tools-tab-cron.php';

				// Handle dynamic tabs
			} else {

				/**
				 * Action to dynamically add content for each tab
				 *
				 */
				do_action('wpbs_submenu_page_tools_tab_' . $tab_slug);
			}
			echo '</div>';
		}
	}

	?>

</div>

