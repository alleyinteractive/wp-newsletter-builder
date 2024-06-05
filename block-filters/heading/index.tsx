/**
 * WordPress dependencies
 */
import { addFilter } from '@wordpress/hooks';

/**
 * Adds border support to Column, Heading, and Paragraph blocks.
 *
 * @param {Object} settings - The original block settings.
 * @param {string} name - The name of the block.
 *
 * @returns {Object} The modified block settings with added border support.
 */
// @ts-ignore
function addBorderSupport(settings, name) {
  // Bail early if the block does not have supports.
  if (!settings?.supports) {
    console.log('here');
    return settings;
  }

  // Only apply to Column, Heading, and Paragraph blocks.
  if (
    name === 'core/heading'
    || name === 'core/paragraph'
  ) {
    console.log('what about here');
    return {
      ...settings,
      supports: Object.assign(settings.supports, {
        __experimentalBorder: {
          color: true,
          style: true,
          width: true,
          radius: true,
          __experimentalDefaultControls: {
            color: false,
            style: false,
            width: false,
            radius: false,
          },
        },
        color: {
          background: false,
          text: false,
        },
      }),
    };
  }

  return settings;
}

addFilter(
  'blocks.registerBlockType',
  'modify-block-supports/add-border-support',
  addBorderSupport,
);
