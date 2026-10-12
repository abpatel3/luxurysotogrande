<!-- Plugin Sticky Header -->
<div class="wpbs-plugin-sticky-header wpbs-plugin-sticky-header-in-tab">

    <!-- Page Title -->
    <span class="wpbs-page-heading">
        <?php echo __('Email Notifications', 'wp-booking-system'); ?>
    </span>

    <!-- Buttons -->
    <div class="wpbs-header-buttons">

        <a href="<?php echo add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'tab' => 'email-notifications', 'email_notification_id' => 'add-new'), admin_url('admin.php'));?>" class="wpbs-primary-button"><?php echo __('Add New Email Notification', 'wp-booking-system'); ?></a>

    </div>

</div>

<!-- Emails List Table -->
<form method="get" class="wpbs-email-notifications-list-table">

    <input type="hidden" name="page" value="wpbs-forms" />
    <input type="hidden" name="subpage" value="edit-form" />
    <input type="hidden" name="form_id" value="<?php echo absint($_GET['form_id']);?>" />
    <input type="hidden" name="tab" value="email-notifications" />
    <input type="hidden" name="paged" value="1">

    <div class="wpbs-box-header wpbs-box-toolbar">
        <?php
        $table = new WPBS_WP_List_Table_Emails($_GET['form_id']);
        $table->views();
        $table->search_box(__('Search Emails', 'wp-booking-system'), 'wpbs-search-emails');
        ?>
    </div>

    <div class="wpbs-box-content wpbs-no-padding">
        <div class="wpbs-box-table-wrapper">
            <?php $table->display(); ?>
        </div>
    </div>

</form>