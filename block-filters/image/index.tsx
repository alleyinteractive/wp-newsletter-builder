import { addFilter } from '@wordpress/hooks';
import domReady from '@wordpress/dom-ready';
import { unregisterBlockStyle } from '@wordpress/blocks';

/**
 * Modifies supports for Image block.
 *
 * @param {Object} settings - The original block settings.
 * @param {string} name - The name of the block.
 *
 * @returns {Object} The modified block settings.
 */
// @ts-ignore
function modifyImageSupports(settings, name) {
  // Bail early if the block does not have supports.
  if (!settings?.supports) {
    return settings;
  }
  // Only apply to Image blocks.
  if (
    name === 'core/image'
  ) {
    return {
      ...settings,
      attributes: Object.assign(settings.attributes, {
        allowResize: { type: 'boolean', default: false },
      }),
      supports: Object.assign(settings.supports, {
        anchor: false,
        align: false,
        alignWide: false,
        customClassName: false,
        dimensions: {
          defaultAspectRatios: false,
        },
        filter: {
          duotone: false,
        },
        __experimentalBorder: {
          color: false,
          radius: true,
          width: false,
          __experimentalSkipSerialization: true,
          __experimentalDefaultControls: {
            color: false,
            radius: true,
            width: false,
          },
        },
        interactivity: false,
      }),
    };
  }
  return settings;
}

addFilter(
  'blocks.registerBlockType',
  'wp-newsletter-builder/image',
  modifyImageSupports,
);

// @ts-ignore
domReady(() => { unregisterBlockStyle('core/image', ['default', 'rounded']); });
