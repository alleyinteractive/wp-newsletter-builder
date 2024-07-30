import { addFilter } from '@wordpress/hooks';

/**
 * Modifies supports for Latest Posts block.
 *
 * @param {Object} settings - The original block settings.
 * @param {string} name - The name of the block.
 *
 * @returns {Object} The modified block settings.
 */
// @ts-ignore
function modifyLatestPostsSupports(settings, name) {
  // Bail early if the block does not have supports.
  if (!settings?.supports) {
    return settings;
  }
  // Only apply to Latest Posts blocks.
  if (
    name === 'core/latest-posts'
  ) {
    return {
      ...settings,
      category: 'wp-newsletter-builder-newsletter',
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
          __experimentalLetterSpacing: false,
          __experimentalFontFamily: false,
          __experimentalFontWeight: false,
          __experimentalFontStyle: false,
          __experimentalTextTransform: false,
        },
      }),
    };
  }
  return settings;
}

addFilter(
  'blocks.registerBlockType',
  'wp-newsletter-builder/latest-posts',
  modifyLatestPostsSupports,
);
