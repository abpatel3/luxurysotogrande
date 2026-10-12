import React from 'react';

const {
  addAction,
} = window?.vendor?.wp?.hooks || {};

const {
  ModuleContainer,
  StyleContainer,
  elementClassnames,
} = window?.divi?.module || {};

const {
  registerModule,
} = window?.divi?.moduleLibrary || {};

const ModuleStyles = ({
  elements,
  settings,
  mode,
  state,
  noStyleTag,
}) => (
  <StyleContainer mode={mode} state={state} noStyleTag={noStyleTag}>
    {elements.style({
      attrName: 'module',
      styleProps: {
        disabledOn: {
          disabledModuleVisibility: settings?.disabledModuleVisibility,
        },
      },
    })}

    {elements.style({
      attrName: 'title',
    })}

    {elements.style({
      attrName: 'content',
    })}
  </StyleContainer>
);

const ModuleScriptData = ({
  elements,
}) => (
  <React.Fragment>
    {elements.scriptData({
      attrName: 'module',
    })}
  </React.Fragment>
);

const moduleClassnames = ({
  classnamesInstance,
  attrs,
}) => {
  classnamesInstance.add(
    elementClassnames({
      attrs: attrs?.module?.decoration ?? {},
    })
  );
};

export function createDiviModule(config) {
  const {
    metadata,
    actionName,
    moduleClassName,
    placeholderTitle,
    placeholderDescription,
    placeholderColor = '#3c82ff',
  } = config;

  const DiviModule = {
    metadata,

    renderers: {
      edit: ({
        attrs,
        id,
        name,
        elements,
      }) => (
        <ModuleContainer
          attrs={attrs}
          elements={elements}
          id={id}
          moduleClassName={moduleClassName}
          name={name}
          scriptDataComponent={ModuleScriptData}
          stylesComponent={ModuleStyles}
          classnamesFunction={moduleClassnames}
        >
          {elements.styleComponents({
            attrName: 'module',
          })}

          <div className="et_pb_module_inner">
            <div
              style={{
                backgroundColor: placeholderColor,
                padding: '30px',
                color: '#ffffff',
              }}
            >
              <h2 style={{ color: '#ffffff' }}>
                {placeholderTitle}
              </h2>

              <p>
                {placeholderDescription}
              </p>
            </div>
          </div>
        </ModuleContainer>
      ),
    },
  };

  if (addAction && registerModule) {
    addAction(
      'divi.moduleLibrary.registerModuleLibraryStore.after',
      actionName,
      () => {
        registerModule(DiviModule.metadata, DiviModule);
      }
    );
  }

  return DiviModule;
}