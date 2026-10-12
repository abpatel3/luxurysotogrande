<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

?>

<div class="wrap wpbs-wrap">

    <?php wpbs_get_wp_admin_header(); ?>

    <!-- Plugin Sticky Header -->
    <div class="wpbs-plugin-sticky-header">

        <!-- Page Title -->
        <span class="wpbs-page-heading"><?php echo __('Add-ons', 'wp-booking-system'); ?></span>

        <!-- Buttons -->
        <div class="wpbs-header-buttons">

        </div>

    </div>

    <!-- WP Notifications -->
    <div class="wpbs-notifications">
        <h1 class="wp-heading-inline"></h1>
    </div>

    <?php if (empty($subscription_type)): ?>
        <!-- No Licence Notice -->
        <div class="notice wpbs-page-notice notice-error">
            <p><?php echo __('Add-ons are only available with a valid licence key.', 'wp-booking-system'); ?></p>
        </div>
    <?php endif ?>


    <!-- Body -->
    <div class="wpbs-plugin-wrapper">
        <div class="wpbs-box wpbs-transparent">

            <div class="wpbs-addons-wrap">

                <?php foreach ($addons as $addon): ?>

                    <div class="wpbs-addon-wrap">
                        <div class="wpbs-addon">
                            <img src="<?php echo $addon['image'] ?>" alt="<?php echo $addon['name'] ?>">

                            <div class="wpbs-addon-inner">
                                <div class="wpbs-addon-inner-text-wrap">
                                    <h2><?php echo $addon['name'] ?></h2>
                                    <p><?php echo $addon['description'] ?></p>
                                </div>
                            </div>

                            <?php if (!empty($subscription_type)): ?>
                                <div class="wpbs-addon-buttons">
                                    <?php if (!array_key_exists($subscription_type, $addon['plans'])): ?>
                                        <p>Only available with the <?php echo implode(' or ', $addon['plans']); ?> licence. <a href="https://www.wpbookingsystem.com/documentation/upgrading-your-license/" target="_blank"><?php echo __('Upgrade your licence', 'wp-booking-system') ?></a></p>
                                    <?php elseif (file_exists(WP_PLUGIN_DIR  . '/' . $addon['slug'] . '/index.php')): ?>
                                        <?php if (is_plugin_active($addon['slug'] . '/index.php')): ?>
                                            <a class="wpbs-primary-button wpbs-disabled-button"><?php echo __('Installed', 'wp-booking-system') ?></a>
                                        <?php else: ?>
                                            <a class="wpbs-primary-button" href="<?php echo wp_nonce_url(add_query_arg(array('page' => 'wpbs-addons', 'wpbs_action' => 'activate_addon', 'addon' => $addon['slug']), admin_url('admin.php')), 'wpbs_activate_addon', 'wpbs_token'); ?>"><?php echo __('Activate', 'wp-booking-system') ?></a>
                                        <?php endif; ?>
                                        <a href="<?php echo $addon['read-more']; ?>" target="_blank" class="wpbs-link-button"><?php _e('View details', 'wp-booking-system') ?></a>
                                    <?php else: ?>
                                        <a class="wpbs-primary-button wpbs-addon-button-install" href="<?php echo wp_nonce_url(add_query_arg(array('page' => 'wpbs-addons', 'wpbs_action' => 'install_addon', 'addon' => $addon['slug']), admin_url('admin.php')), 'wpbs_install_addon', 'wpbs_token'); ?>"><?php echo __('Install', 'wp-booking-system') ?></a>
                                        <a href="<?php echo $addon['read-more']; ?>" target="_blank" class="wpbs-link-button"><?php _e('View details', 'wp-booking-system') ?></a>
                                    <?php endif; ?>
                                </div>

                            <?php endif; ?>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

        </div>
    </div>

</div>