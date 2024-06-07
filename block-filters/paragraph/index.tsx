import { addFilter } from '@wordpress/hooks';

/**
 * Modifies supports for Paragraph block.
 *
 * @param {Object} settings - The original block settings.
 * @param {string} name - The name of the block.
 *
 * @returns {Object} The modified block settings.
 */
// @ts-ignore
function modifyParagraphSupports(settings, name) {
  // Bail early if the block does not have supports.
  if (!settings?.supports) {
    return settings;
  }
  // Only apply to paragraph blocks.
  if (
    name === 'core/paragraph'
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
  'wp-newsletter-builder/paragraph',
  modifyParagraphSupports,
);
