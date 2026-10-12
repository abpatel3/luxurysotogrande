import metadata from './module.json';

import { createDiviModule } from '../create-divi-module';
import { registerFieldOptions } from '../register-field-options';

createDiviModule({
  metadata,
  actionName: 'WPBSDivi5.WPBS_Yearly_Calendar',
  moduleClassName: 'wpbs_divi5_yearly_calendar',
  placeholderTitle: 'WP Booking System - Yearly Calendar Widget',
  placeholderDescription: 'This placeholder will be replaced with the Calendar when viewing the page. Configure your calendar options from the Sidebar.',
  placeholderColor: '#3c82ff',
});

registerFieldOptions(metadata.name, [
  {
    field: 'id',
    filterName: 'wpbs/yearly-calendar-options',
    optionsGlobal: 'wpbsDiviCalendarOptions',
  },
  {
    field: 'form_id',
    filterName: 'wpbs/yearly-form-options',
    optionsGlobal: 'wpbsDiviFormOptions',
  },
  {
    field: 'language',
    filterName: 'wpbs/yearly-language-options',
    optionsGlobal: 'wpbsDiviLangOptions',
  },
  {
    field: 'start_year',
    filterName: 'wpbs/yearly-start-year-options',
    optionsGlobal: 'wpbsDiviStartYearOptions',
  }
]);