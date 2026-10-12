import metadata from './module.json';

import { createDiviModule } from '../create-divi-module';
import { registerFieldOptions } from '../register-field-options';

createDiviModule({
  metadata,
  actionName: 'WPBSDivi5.WPBS_Single_Calendar',
  moduleClassName: 'wpbs_divi5_single_calendar',
  placeholderTitle: 'WP Booking System - Single Calendar Widget',
  placeholderDescription: 'This placeholder will be replaced with the Calendar when viewing the page. Configure your calendar options from the Sidebar.',
  placeholderColor: '#3c82ff',
});

registerFieldOptions(metadata.name, [
  {
    field: 'id',
    filterName: 'wpbs/single-calendar-options',
    optionsGlobal: 'wpbsDiviCalendarOptions',
  },
  {
    field: 'form_id',
    filterName: 'wpbs/single-form-options',
    optionsGlobal: 'wpbsDiviFormOptions',
  },
  {
    field: 'language',
    filterName: 'wpbs/single-language-options',
    optionsGlobal: 'wpbsDiviLangOptions',
  },
  {
    field: 'start_month',
    filterName: 'wpbs/single-start-month-options',
    optionsGlobal: 'wpbsDiviStartMonthOptions',
  },
  {
    field: 'start_year',
    filterName: 'wpbs/single-start-year-options',
    optionsGlobal: 'wpbsDiviStartYearOptions',
  },
  {
    field: 'months',
    filterName: 'wpbs/single-months-display-options',
    optionsGlobal: 'wpbsDiviMonthsDisplayOptions',
  },
]);