export function registerFieldOptions(moduleName, fields) {
  if (!window.vendor?.wp?.hooks || !window.lodash) {
    return;
  }

  const { addFilter } = window.vendor.wp.hooks;
  const { cloneDeep, set } = window.lodash;

  const diviModuleName = moduleName.replace(/\//g, '.');
  const hookName = `divi.moduleLibrary.moduleAttributes.${diviModuleName}`;

  fields.forEach((fieldConfig) => {
    const {
      field,
      filterName,
      optionsGlobal,
    } = fieldConfig;

    addFilter(
      hookName,
      filterName,
      function (attributes) {
        const updated = cloneDeep(attributes);

        set(
          updated,
          [field, 'settings', 'innerContent', 'item', 'component', 'props', 'options'],
          window[optionsGlobal] || {}
        );

        return updated;
      }
    );
  });
}