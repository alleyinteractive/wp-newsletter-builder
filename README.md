# Newsletter Builder

Contributors: alleyinteractive

Tags: alleyinteractive, wp-newsletter-builder

Stable tag: 0.3.6

Requires at least: 6.2

Tested up to: 6.4.1

Requires PHP: 8.0

License: GPL v2 or later

[![Coding Standards](https://github.com/alleyinteractive/wp-newsletter-builder/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/alleyinteractive/wp-newsletter-builder/actions/workflows/coding-standards.yml)
[![Testing Suite](https://github.com/alleyinteractive/wp-newsletter-builder/actions/workflows/unit-test.yml/badge.svg)](https://github.com/alleyinteractive/wp-newsletter-builder/actions/workflows/unit-test.yml)

Interface to manage email newsletters. Creates a Custom Post Type.

## Installation

You can install the package via composer:

```bash
composer require alleyinteractive/wp-newsletter-builder
```

## Usage

Activate the plugin in WordPress and use it like so:

```php
$plugin = WP_Newsletter_Builder\WP_Newsletter_Builder\WP_Newsletter_Builder();
$plugin->perform_magic();
```
### Enabling an Email Provider

The plugin supports multiple email providers. To enable an email provider, add the following code as a plugin or function in your theme  `wp-newsletter-builder-PROVIDER.php`:

This decision was made so that only developers can switch email providers (but it may be that we want to create a settings page for this in the future.)

```php
<?php
/**
 * Plugin Name: PROVIDER for WP Newsletter Builder
 * Description: Plugin to enable PROVIDER as an email provider for WP Newsletter Builder.
 * Version: 0.1.0
 * Author: Alley
 *
 * Text Domain: newsletter-testing
 * Domain Path: /languages/
 *
 * @package newsletter-testing
 */
add_filter( 'wp_newsletter_builder_selected_provider',
	fn( $provider ) => 'WP_Newsletter_Builder\Email_Providers\PROVIDER'
);
```
### Filtering Post Types Available in the Post Picker

The plugin allows filtering of post types available in the Gutenberg post picker. The post picker appears in the `wp-newsletter-builder/post` block as a single post picker and in the `wp-newsletter-builder/section` block as a multiple post picker.

For example, to allow a custom `podcast` post type to appear in the post picker, along with the default `post` post type:

```php
add_filter( 'wp_newsletter_builder_allowed_post_types',
  function( array $allowed_post_types ) {
    $allowed_post_types[] = 'podcast';
    return $allowed_post_types;
  }
);
```

## Testing

Run `npm run test` to run Jest tests against JavaScript files. Run
`npm run test:watch` to keep the test runner open and watching for changes.

Run `npm run lint` to run ESLint against all JavaScript files. Linting will also
happen when running development or production builds.

Run `composer test` to run tests against PHPUnit and the PHP code in the plugin.

### The `entries` directory and entry points

All directories created in the `entries` directory can serve as entry points and will be compiled with [@wordpress/scripts](https://github.com/WordPress/gutenberg/blob/trunk/packages/scripts/README.md#scripts) into the `build` directory with an accompanied `index.asset.php` asset map.

#### Enqueuing Entry Points

You can also include an `index.php` file in the entry point directory for enqueueing or registering a script. This file will then be moved to the build directory and will be auto-loaded with the `load_scripts()` function in the `functions.php` file. Alternatively, if a script is to be enqueued elsewhere there are helper functions in the `src/assets.php` file for getting the assets.

### Scaffold a block with `create-block`

Use the `create-block` command to create custom blocks with [`@wordpress/create-block`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-create-block/) and follow the prompts to generate all the block assets in the `blocks/` directory.
Block registration, script creation, etc will be scaffolded from the `bin/create-block/templates/block/` templates. Run `npm run build` to compile and build the custom block. Blocks are enqueued using the `load_scripts()` function in `src/assets.php`.

### Updating WP Dependencies

Update the [WordPress dependency packages](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/#packages-update) used in the project to their latest version.

To update `@wordpress` dependencies to their latest version use the packages-update command:

```sh
npx wp-scripts packages-update
```

This script provides the following custom options:

-   `--dist-tag` – allows specifying a custom dist-tag when updating npm packages. Defaults to `latest`. This is especially useful when using [`@wordpress/dependency-extraction-webpack-plugin`](https://www.npmjs.com/package/@wordpress/dependency-extraction-webpack-plugin). It lets installing the npm dependencies at versions used by the given WordPress major version for local testing, etc. Example:

```sh
npx wp-scripts packages-update --dist-tag=wp-WPVERSION`
```

Where `WPVERSION` is the version of WordPress you are targeting. The version
must include both the major and minor version (e.g., `6.1`). For example:

```sh
npx wp-scripts packages-update --dist-tag=wp-6.1`
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

This project is actively maintained by [Alley
Interactive](https://github.com/alleyinteractive). Like what you see? [Come work
with us](https://alley.co/careers/).

- [Alley Interactive](https://github.com/Alley Interactive)
- [All Contributors](../../contributors)

## License

The GNU General Public License (GPL) license. Please see [License File](LICENSE) for more information.
