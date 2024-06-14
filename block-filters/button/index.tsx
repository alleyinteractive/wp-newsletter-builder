import { addFilter } from '@wordpress/hooks';

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
  // Only apply to Button blocks.
  if (
    (name === 'core/button') || (name === 'core/buttons')
  ) {
    return {
      ...settings,
      supports: Object.assign(settings.supports, {
        anchor: false,
        color: {
          background: false,
          text: false,
        },
        customClassName: false,
        inserter: false,
        layout: false,
        shadow: false,
        spacing: false,
        styles: [],
        typography: {
          __experimentalFontSize: false,
          __experimentalLineHeight: false,
          __experimentalLetterSpacing: true,
          __experimentalFontFamily: false,
          __experimentalFontWeight: false,
          __experimentalFontStyle: false,
          __experimentalTextTransform: true,
        },
        __experimentalBorder: {
          color: false,
          radius: true,
          style: true,
          width: false,
          __experimentalSkipSerialization: true,
          __experimentalDefaultControls: {
            color: false,
            radius: true,
            style: true,
            width: false,
          },
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
