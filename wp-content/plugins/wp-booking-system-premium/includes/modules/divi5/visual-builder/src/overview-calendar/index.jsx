import metadata from './module.json';

import { createDiviModule } from '../create-divi-module';
import { registerFieldOptions } from '../register-field-options';

createDiviModule({
  metadata,
  actionName: 'WPBSDivi5.WPBS_Overview_Calendar',
  moduleClassName: 'wpbs_divi5_overview_calendar',
  placeholderTitle: 'WP Booking System - Overview Calendar Widget',
  placeholderDescription: 'This placeholder will be replaced with the Calendar when viewing the page. Configure your calendar options from the Sidebar.',
  placeholderColor: '#3c82ff',
});

registerFieldOptions(metadata.name, [
  {
    field: 'calendars',
    filterName: 'wpbs/overview-calendar-options',
    optionsGlobal: 'wpbsDiviCalendarOptions',
  },
  {
    field: 'language',
    filterName: 'wpbs/overview-language-options',
    optionsGlobal: 'wpbsDiviLangOptions',
  },
  {
    field: 'start_month',
    filterName: 'wpbs/overview-start-month-options',
    optionsGlobal: 'wpbsDiviStartMonthOptions',
  },
  {
    field: 'start_year',
    filterName: 'wpbs/overview-start-year-options',
    optionsGlobal: 'wpbsDiviStartYearOptions',
  }
]);