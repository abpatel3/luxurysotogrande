<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

?>

<div class="wrap wpbs-wrap wpbs-wrap-forms">

	<?php wpbs_get_wp_admin_header(); ?>

	<!-- Plugin Sticky Header -->
	<div class="wpbs-plugin-sticky-header">

		<!-- Page Title -->
		<span class="wpbs-page-heading"><?php echo __('Forms', 'wp-booking-system'); ?></span>

		<!-- Buttons -->
		<div class="wpbs-header-buttons">
			<a href="<?php echo add_query_arg(array('subpage' => 'add-form'), $this->admin_url); ?>" class="wpbs-primary-button"><?php echo __('Add New Form', 'wp-booking-system'); ?></a>
		</div>

	</div>

	<!-- WP Notifications -->
	<div class="wpbs-notifications">
		<h1 class="wp-heading-inline"></h1>
	</div>

	<!-- Body -->
	<div class="wpbs-plugin-wrapper">
		<div class="wpbs-box wpbs-transparent">

			<!-- Forms List Table -->
			<form method="get">

				<input type="hidden" name="page" value="wpbs-forms" />
				<input type="hidden" name="paged" value="1">

				<div class="wpbs-box-header wpbs-box-toolbar">
					<?php
					$table = new WPBS_WP_List_Table_Forms();
					$table->views();
					$table->search_box(__('Search Forms', 'wp-booking-system'), 'wpbs-search-forms');
					?>
				</div>

				<div class="wpbs-box-content wpbs-no-padding">
					<div class="wpbs-box-table-wrapper">
						<?php $table->display(); ?>
					</div>
				</div>

			</form>

		</div>
	</div>

</div>