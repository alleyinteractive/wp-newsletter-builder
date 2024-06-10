import { addFilter } from '@wordpress/hooks';

/**
 * Modifies supports for List block.
 *
 * @param {Object} settings - The original block settings.
 * @param {string} name - The name of the block.
 *
 * @returns {Object} The modified block settings.
 */
// @ts-ignore
function modifyListSupports(settings, name) {
  // Bail early if the block does not have supports.
  if (!settings?.supports) {
    return settings;
  }
  // Only apply to list and list item blocks.
  if (
    name === 'core/list'
    || name === 'core/list-item'
  ) {
    return {
      ...settings,
      supports: Object.assign(settings.supports, {
        align: [],
        anchor: false,
        color: {
          background: false,
          text: false,
        },
        customClassName: false,
        inserter: false,
        spacing: false,
        typography: {
          __experimentalFontSize: false,
          __experimentalLineHeight: false,
          __experimentalLetterSpacing: true,
          __experimentalFontFamily: false,
          __experimentalFontWeight: false,
          __experimentalFontStyle: false,
          __experimentalTextTransform: true,
        },
      }),
    };
  }

  return settings;
}

addFilter(
  'blocks.registerBlockType',
  'wp-newsletter-builder/list',
  modifyListSupports,
);
