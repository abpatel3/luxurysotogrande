<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$form_id   = absint(! empty($_GET['form_id']) ? $_GET['form_id'] : 0);
$form      = wpbs_get_form($form_id);

if (is_null($form))
    return;

$form_meta = wpbs_get_form_meta($form_id);
$form_data = $form->get('fields');

$tabs         = $this->get_tabs();
$active_tab = (! empty($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'form-builder');

$settings = get_option('wpbs_settings', array());
$active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());
$languages = wpbs_get_languages();

$removable_query_args = wp_removable_query_args();

$available_field_types = wpbs_form_available_field_types();
$available_field_types_options = wpbs_form_available_field_types_options();

$vat = new WPBS_VAT();

$disable_form_tag = false;
// Disable form and save changes buttons
if($active_tab != 'email-notifications'){
    $disable_form_tag = true;
}

?>

<script>
    var wpbs_languages = <?php echo json_encode($languages); ?>;
    var wpbs_form_data = <?php echo json_encode($form_data); ?>;
    var wpbs_available_field_types = <?php echo json_encode($available_field_types); ?>;
    var wpbs_available_field_types_options = <?php echo json_encode($available_field_types_options); ?>;
</script>

<?php if ($warnings = wpbs_form_notifications_warnings($form_id, $form_data, $form_meta)): ?>
    <?php foreach ($warnings as $warning): ?>
        <div class="wpbs-page-notice notice notice-error">
            <p><?php echo $warning; ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="wrap wpbs-wrap wpbs-wrap-edit-form wpbs-wrap-edit-form-vat-<?php echo $vat->is_enabled() ? 'enabled' : 'disabled'; ?>">

    <?php wpbs_get_wp_admin_header(); ?>

    <?php if($disable_form_tag): ?>
        <form method="POST" action="" autocomplete="off">
    <?php endif;  ?>

        <!-- Plugin Sticky Header -->
        <div class="wpbs-plugin-sticky-header">

            <!-- Page Title -->
            <span class="wpbs-page-heading">
                <?php echo __('Edit Form', 'wp-booking-system'); ?>

                <!-- Form Title -->
                <div id="titlediv" class="wpbs-page-heading-editable">
                    <div id="titlewrap">
                        <div class="wpbs-form-field">
                            <div class="wpbs-input">
                                <input type="text" name="form_name" size="30" value="<?php echo esc_attr($form->get('name')) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <span class="wpbs-heading-tag"><strong><?php printf(__('Form ID: %d', 'wp-booking-system'), $form_id); ?></strong></span>

            </span>

            <!-- Buttons -->
            <div class="wpbs-header-buttons">

                <!-- Back Button -->
                <a href="<?php echo add_query_arg(array('page' => 'wpbs-forms'), admin_url('admin.php')); ?>" class="wpbs-secondary-button"><?php echo __('Back to all forms', 'wp-booking-system') ?></a>
                <?php if($disable_form_tag): ?>
                    <a href="#" class="wpbs-primary-button wpbs-custom-save-button"><?php echo __('Save Changes', 'wp-booking-system'); ?></a>
                <?php endif; ?>

            </div>

        </div>

        <!-- WP Notifications -->
        <div class="wpbs-notifications">
            <h1 class="wp-heading-inline"></h1>
        </div>

        <div class="wpbs-plugin-wrapper">
            <div class="wpbs-box">



                <!-- Navigation Tabs -->
                <div class="wpbs-tabs-nav">
                    <nav class="wpbs-tabs-menu">
                        <ul>
                            <?php
                            if (! empty($tabs)) {
                                foreach ($tabs as $tab_slug => $tab_name) {

                                    echo '<li><a href="' . add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'tab' => $tab_slug), admin_url('admin.php')) . '" data-tab="' . $tab_slug . '" class="wpbs-nav-tab ' . ($active_tab == $tab_slug ? 'wpbs-nav-tab-active' : '') . '">' . $tab_name . '</a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </nav>
                </div>

                <div class="wpbs-box-content wpbs-active-tab-<?php echo $active_tab;?> <?php echo isset($_GET['email_notification_id']) && absint($_GET['email_notification_id']) != 0 ? 'wpbs-email-notification-edit-screen' : '';?>">
                    <!-- Tabs Contents -->
                    <div class="wpbs-tab-wrapper">

                        <?php

                        if (! empty($tabs)) {

                            foreach ($tabs as $tab_slug => $tab_name) {

                                if ($active_tab != $tab_slug) {
                                    continue;
                                }

                                echo '<div class="wpbs-tab wpbs-tab-' . $tab_slug . ' ' . ($active_tab == $tab_slug ? 'wpbs-active' : '') . '" data-tab="' . $tab_slug . '">';

                                switch ($tab_slug) {
                                    case 'form-builder':
                                        include 'view-edit-form-tab-form-builder.php';
                                        break;

                                    case 'form-options':
                                        include 'view-edit-form-tab-form-options.php';
                                        break;

                                    case 'email-notifications':
                                        include 'view-edit-form-tab-email-notifications.php';
                                        break;

                                    case 'form-strings':
                                        include 'view-edit-form-tab-form-strings.php';
                                        break;

                                    default:
                                        /**
                                         * Action to dynamically add content for each tab
                                         *
                                         */
                                        do_action('wpbs_submenu_page_edit_form_tab_' . $tab_slug);
                                }

                                echo '</div>';
                            }
                        }

                        ?>
                    </div>
                </div>

                <?php if($disable_form_tag): ?>
                    <!-- Hidden fields -->
                    <input type="hidden" name="form_id" value="<?php echo $form_id; ?>" />
                    <input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr(wp_unslash($_SERVER['REQUEST_URI'])); ?>">

                    <!-- Nonce -->
                    <?php wp_nonce_field('wpbs_edit_form', 'wpbs_token', false); ?>
                    <input type="hidden" name="wpbs_action" value="edit_form" />

                    <!-- Save Page Button -->
                    <div class="wpbs-box-footer-wrapper">
                        <div class="wpbs-box-footer">
                            <input type="submit" class="wpbs-primary-button wpbs-save-form" value="<?php echo __('Save Form', 'wp-booking-system'); ?>" />
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    <?php if($disable_form_tag): ?> 
    </form>
    <?php endif; ?>

</div>