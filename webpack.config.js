const path = require('path');

const defaultConfig = require('@alleyinteractive/build-tool/dist/cjs/config/webpack.config');

/**
 * alley-build deep merges this object into its own webpack config, so this
 * file must export a plain object (not a function) and only needs to declare
 * what differs from the default.
 *
 * Capture the default entry function now: alley-build replaces
 * `defaultConfig.default.entry` with the one below when it merges.
 *
 * @see node_modules/@alleyinteractive/build-tool/README.md#extending-the-config
 */
const { entry: defaultEntry } = defaultConfig.default;

module.exports = {
  // Blocks and entries/* come from the default; add the other custom entry points.
  entry: () => ({
    ...defaultEntry(),
    'wp-newsletter-builder-button/index': './block-filters/button',
    'wp-newsletter-builder-heading/index': './block-filters/heading',
    'wp-newsletter-builder-image/index': './block-filters/image',
    'wp-newsletter-builder-latest-posts/index': './block-filters/latest-posts',
    'wp-newsletter-builder-list/index': './block-filters/list',
    'wp-newsletter-builder-paragraph/index': './block-filters/paragraph',
    'wp-newsletter-builder-separator/index': './block-filters/separator',
    'wp-newsletter-builder-from-post/index': './plugins/newsletter-from-post',
    'newsletter-status/index': './plugins/newsletter-status',
    'newsletter-template-styles/index': './plugins/newsletter-template-styles',
    'pre-publish-checks/index': './plugins/pre-publish-checks',
  }),

  resolve: {
    alias: {
      '@': path.resolve(__dirname),
    },
    extensions: ['.js', '.jsx', '.ts', '.tsx', '.scss', '...'],
  },

  // Cache the generated webpack modules and chunks to improve build speed.
  // @see https://webpack.js.org/configuration/cache/
  cache: {
    type: 'filesystem',
  },
};
