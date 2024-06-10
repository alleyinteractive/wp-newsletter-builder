import { addFilter } from '@wordpress/hooks';
// import { createHigherOrderComponent } from '@wordpress/compose';

/**
 * Modifies supports for Heading block.
 *
 * @param {Object} settings - The original block settings.
 * @param {string} name - The name of the block.
 *
 * @returns {Object} The modified block settings.
 */
// @ts-ignore
function modifyHeadingSupports(settings, name) {
  // Bail early if the block does not have supports.
  if (!settings?.supports) {
    return settings;
  }
  // Only apply to Heading blocks.
  if (
    name === 'core/heading'
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
  'wp-newsletter-builder/heading',
  modifyHeadingSupports,
);
