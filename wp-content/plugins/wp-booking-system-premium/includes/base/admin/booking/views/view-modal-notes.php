<div class="wpbs-tab-inner wpbs-booking-details-modal-notes">

    <h2>
        <?php echo __('Notes', 'wp-booking-system') ?>
        <small class="wpbs-heading-description"><?php echo __('Notes are only visible to website administrators, they are not sent to the client.', 'wp-booking-system') ?></small>
    </h2>

    <div class="wpbs-booking-details-modal-notes-wrap">

        <?php if ($this->get_notes()): ?>

            <?php foreach ($this->get_notes() as $i => $note): ?>

                <div class="wpbs-booking-details-modal-note">

                    <div class="wpbs-booking-details-modal-note-body">
                        <p><?php echo nl2br($note['note']); ?></p>
                    </div>
                    <div class="wpbs-booking-details-modal-note-footer">

                        <p><?php echo __('Added on', 'wp-booking-system') ?>: <?php echo wpbs_date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $note['timestamp']); ?></p>
                        <a href="#" data-booking-note="<?php echo $i; ?>" data-booking-id="<?php echo $this->booking->get('id'); ?>" class="wpbs-booking-details-modal-note-remove"><i class="wpbs-icon-trash"></i></a>


                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p class="wpbs-booking-details-modal-note-no-results"><?php echo __('No notes found for this booking.', 'wp-booking-system') ?></p>

        <?php endif; ?>

    </div>

    <div class="wpbs-divider"></div>

    <h2><?php echo __('Add New Note', 'wp-booking-system') ?></h2>

    <form class="wpbs-booking-details-modal-notes-add-new">

        <div class="wpbs-form-field">
            <div class="wpbs-input">
                <textarea id="wpbs_modal_booking_note" rows="5" placeholder="<?php echo __('Start writing your note here...', 'wp-booking-system'); ?>"></textarea>
            </div>
        </div>

    </form>

</div>


<!-- Save Page Button -->
<div class="wpbs-box-footer-wrapper">
    <div class="wpbs-box-footer">
        <button class="wpbs-primary-button" id="wpbs_modal_add_booking_note" data-booking-id="<?php echo $this->booking->get('id'); ?>"><?php echo __('Add Note', 'wp-booking-system') ?></button>
    </div>
</div>