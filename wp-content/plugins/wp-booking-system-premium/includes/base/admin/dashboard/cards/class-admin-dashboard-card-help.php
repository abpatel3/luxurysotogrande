<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


/**
 * Dashboard card: Need Help.
 *
 */
class WPBS_Admin_Dashboard_Card_Help extends WPBS_Admin_Dashboard_Card
{

    /**
     * Initialize the card.
     *
     */
    protected function init()
    {

        $this->slug    = 'wpbs_card_need_help';
        $this->name    = __('Need help?', 'wp-booking-system');
        $this->context = 'primary';
    }


    /**
     * Output the card's content.
     *
     */
    public function output()
    {

?>
        <div class="wpbs-card-need-help-content">
            <p><?php echo __('Need help setting up WP Booking System or have any questions about the plugin?', 'wp-booking-system'); ?></p>
            <div class="wpbs-buttons">
                <a href="https://www.wpbookingsystem.com/documentation/" target="_blank" class="wpbs-primary-button"><?php echo __('Read the documentation', 'wp-booking-system'); ?></a>
                <a href="https://www.wpbookingsystem.com/contact/" target="_blank" class="wpbs-secondary-button"><?php echo __('Open a support ticket', 'wp-booking-system'); ?></a>
            </div>
        </div>

<?php

    }
}
