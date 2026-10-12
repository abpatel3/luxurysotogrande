<?php 
$email_id = isset($_GET['email_notification_id']) && $_GET['email_notification_id'] != '' ? absint($_GET['email_notification_id']) : false;
$email      = wpbs_get_email($email_id);

if ($email_id === 0) {
	include WPBS_PLUGIN_DIR . 'includes/base/admin/email/views/view-add-email.php';
} elseif($email_id > 0){
	include WPBS_PLUGIN_DIR . 'includes/base/admin/email/views/view-edit-email.php';
} else {
	include WPBS_PLUGIN_DIR . 'includes/base/admin/email/views/view-emails.php';
}