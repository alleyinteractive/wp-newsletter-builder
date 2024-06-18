const path = require('path');
const fs = require('fs');

const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = (env, { mode }) => ({
  ...defaultConfig,

  // Dynamically produce entries from the slotfills index file and all blocks.
  entry: () => {
    const blocks = defaultConfig.entry();

    return {
      ...blocks,
      ...fs
        .readdirSync('./entries')
        .reduce((acc, dirPath) => {
          acc[
            `entries-${dirPath}`
          ] = `./entries/${dirPath}`;
          return acc;
        }, {
          // All other custom entry points can be included here.
          'wp-newsletter-builder-button/index': './block-filters/button',
          'wp-newsletter-builder-heading/index': './block-filters/heading',
          'wp-newsletter-builder-image/index': './block-filters/image',
          'wp-newsletter-builder-list/index': './block-filters/list',
          'wp-newsletter-builder-paragraph/index': './block-filters/paragraph',
          'wp-newsletter-builder-separator/index': './block-filters/separator',
          'wp-newsletter-builder-from-post/index': './plugins/newsletter-from-post',
          'newsletter-status/index': './plugins/newsletter-status',
          'pre-publish-checks/index': './plugins/pre-publish-checks',
        }),
    };
  },

  // Use different filenames for production and development builds for clarity.
  output: {
    clean: mode === 'production',
    filename: (pathData) => {
      const dirname = pathData.chunk.name;

      // Process all non-entries entries.
      if (!pathData.chunk.name.includes('entries-')) {
        return '[name].js';
      }

      const srcDirname = dirname.replace('entries-', '');
      return `${srcDirname}/index.js`;
    },
    path: path.join(__dirname, 'build'),
  },

  // Configure plugins.
  plugins: [
    ...defaultConfig.plugins,
    new CopyWebpackPlugin({
      patterns: [
        {
          from: '**/{index.php,*.css}',
          context: 'entries',
          noErrorOnMissing: true,
        },
      ],
    }),
    new MiniCssExtractPlugin({
      filename: (pathData) => {
        const dirname = pathData.chunk.name;
        // Process all blocks.
        if (!pathData.chunk.name.includes('entries-')) {
          return '[name].css';
        }

        const srcDirname = dirname.replace('entries-', '');
        return `${srcDirname}/index.css`;
      },
    }),
    new CleanWebpackPlugin({
      cleanAfterEveryBuildPatterns: [
        /**
         * Remove duplicate entry CSS files generated from default
         * MiniCssExtractPlugin plugin in wpScripts.
         *
         * The default MiniCssExtractPlugin filename is [name].css
         * resulting in the generation of the entries-*.css files.
         * The configuration in this file for MiniCssExtractPlugin outputs
         * the entry CSS into the entry src directory name.
         */
        'entries-*.css',
        // Maps are built when running the start mode with wpScripts.
        'entries-*.css.map',
      ],
      protectWebpackAssets: false,
    }),
  ],

  // This webpack alias rule is needed at the root to ensure that the paths are resolved
  // using the custom alias defined below.
  resolve: {
    alias: {
      ...defaultConfig.resolve.alias,
      '@': path.resolve(__dirname),
    },
    extensions: ['.js', '.jsx', '.ts', '.tsx', '.scss', '...'],
  },

  // Cache the generated webpack modules and chunks to improve build speed.
  // @see https://webpack.js.org/configuration/cache/
  cache: {
    ...defaultConfig.cache,
    type: 'filesystem',
  },
  devServer: mode === 'production' ? {} : {
    ...defaultConfig.devServer,
    allowedHosts: 'all',
    static: {
      directory: '/build',
    },
  },
});
