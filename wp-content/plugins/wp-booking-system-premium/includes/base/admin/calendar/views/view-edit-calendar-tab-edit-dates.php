<div id="wpbs-calendar-events">
    <?php
    $calendar_args = array(
        'current_year' => $current_year,
        'current_month' => $current_month,
    );

    $calendar_editor = new WPBS_Calendar_Editor_Outputter($calendar, $calendar_args);
    $calendar_editor->display();


    ?>
</div>