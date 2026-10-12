<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$user_roles = get_editable_roles();

?>

<!-- User Role Permissions -->
<div class="wpbs-form-field">

    <label class="wpbs-label">
        <?php echo __('User Role Permissions', 'wp-booking-system'); ?>
    </label>

    <div class="wpbs-input wpbs-chosen-wrapper">

        <select multiple name="wpbs_settings[user_role_permissions][]" class="wpbs-chosen">
            <?php
            foreach ($user_roles as $user_role_slug => $user_role) {

                if ($user_role_slug == 'administrator')
                    continue;

                echo '<option value="' . esc_attr($user_role_slug) . '" ' . (! empty($settings['user_role_permissions']) && in_array($user_role_slug, $settings['user_role_permissions']) ? 'selected' : '') . '>' . $user_role['name'] . '</option>';
            }
            ?>
        </select>

    </div>

    <div class="wpbs-label-description"><?php echo __('Select the user roles you wish to have editing capabilities. All users that have the selected user roles will be able to edit all forms and calendars.', 'wp-booking-system'); ?></div>

</div>