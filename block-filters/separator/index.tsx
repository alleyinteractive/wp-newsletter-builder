import { addFilter } from '@wordpress/hooks';

addFilter(
  'blocks.registerBlockType',
  'newsletter-builder/separator',
  (settings) => ({
    ...settings,
    attributes: {
      ...settings.attributes,
      hasSeparator: {
        type: 'boolean',
        default: false,
      },
      separatorIsWide: {
        type: 'boolean',
        default: false,
      },
    },
  }),
);
