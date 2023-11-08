<?php
/**
 * Plugin Name: Newsletter Builder
 * Plugin URI: https://github.com/alleyinteractive/wp-newsletter-builder
 * Description: Interface to manage email newsletters
 * Version: 0.3.0
 * Author: Alley Interactive
 * Author URI: https://github.com/alleyinteractive/wp-newsletter-builder
 * Requires at least: 5.9
 * Tested up to: 6.2
 *
 * Text Domain: wp-newsletter-builder
 * Domain Path: /languages/
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Root directory to this plugin.
 *
 * @var string
 */
define( 'WP_NEWSLETTER_BUILDER_DIR', __DIR__ );

// Check if Composer is installed (remove if Composer is not required for your plugin).
if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	\add_action(
		'admin_notices',
		function () {
			?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'Composer is not installed and wp-newsletter-builder cannot load. Try using a `*-built` branch if the plugin is being loaded as a submodule.', 'wp-newsletter-builder' ); ?></p>
			</div>
			<?php
		}
	);

	return;
}

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';

// Load the plugin's main files.
require_once __DIR__ . '/src/assets.php';
require_once __DIR__ . '/src/meta.php';
require_once __DIR__ . '/src/utils.php';
require_once __DIR__ . '/block-filters/separator/index.php';

require_once __DIR__ . '/plugins/newsletter-from-post/index.php';
require_once __DIR__ . '/plugins/newsletter-status/index.php';

/* class files get loaded by the autoloader */

global $newsletter_builder_email_provider;

/**
 * Instantiate the plugin.
 */
function main() {
	new Ads();
	new Breaking_Recipients();
	new Email_Types();
	new Settings();
	new Media();
	new WP_Newsletter_Builder();
	new Rest_API_Endpoints();
	new Rest_API_Fields();
	new Rest_API_Query();
	// Find selected email provider and instantiate it.
	$selected_email_provider = apply_filters( 'wp_newsletter_builder_selected_provider', '' );

	// Check if provider has been selected and exists.
	if ( empty( $selected_email_provider ) || ! class_exists( $selected_email_provider ) ) {
		\add_action(
			'admin_notices',
			function () {
				?>
				<div class="notice notice-error">
					<p>
						<?php
						printf(
							// translators: %s is the filter name.
							esc_html__(
								'No email provider selected for WP Newsletter Builder. Use the %s filter to specify one.',
								'wp-newsletter-builder'
							),
							'<code>wp_newsletter_builder_selected_provider</code>'
						);
						?>
					</p>
				</div>
				<?php
			}
		);

		return;
	}
	global $newsletter_builder_email_provider;
	$provider = new $selected_email_provider();

	$newsletter_builder_email_provider = new $provider();
	$newsletter_builder_email_provider->setup();
}
main();
