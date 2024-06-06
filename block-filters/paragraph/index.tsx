import { addFilter } from '@wordpress/hooks';

/**
 * Modifies supports for Paragraph block.
 * https://nickdiego.com/how-to-modify-block-supports-using-client-side-filters/
 *
 * @param {Object} settings - The original block settings.
 * @param {string} name - The name of the block.
 *
 * @returns {Object} The modified block settings with added border support.
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
          __experimentalDropCap: false,
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
