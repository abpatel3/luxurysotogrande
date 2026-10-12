<!-- Notes -->

<div class="wpbs-tab-inner">

    <h2><?php echo __('Notes', 'wp-booking-system'); ?></h2>


    <div class="wpbs-calendar-notes">

        <?php wpbs_output_notes_html($calendar_id); ?>

        <div class="wpbs-form-field">
            <div class="wpbs-label"><?php echo __('Add new note', 'wp-booking-system'); ?></div>
            <div class="wpbs-input"><textarea name="wpbs-calendar-note-content" id="wpbs-calendar-note-content" placeholder="<?php echo __('Start writing your note here...', 'wp-booking-system'); ?>" cols="30" rows="10"></textarea></div>
        </div>

    </div>

</div>

<div class="wpbs-box-footer-wrapper wpbs-calendar-new-note">
    <div class="wpbs-box-footer">
        <button class="wpbs-secondary-button" id="wpbs-calendar-add-note"><?php echo __('Add Note', 'wp-booking-system'); ?></button>
    </div>
</div>