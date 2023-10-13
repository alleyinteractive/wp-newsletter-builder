<?php
/**
 * Plugin Name: Newsletter Builder
 * Plugin URI: https://github.com/alleyinteractive/newsletter-builder
 * Description: Interface to manage email newsletters
 * Version: 0.1.0
 * Author: Alley Interactive
 * Author URI: https://github.com/alleyinteractive/newsletter-builder
 * Requires at least: 5.9
 * Tested up to: 6.2
 *
 * Text Domain: newsletter-builder
 * Domain Path: /languages/
 *
 * @package newsletter-builder
 */

namespace Newsletter_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Root directory to this plugin.
 *
 * @var string
 */
define( 'NEWSLETTER_BUILDER_DIR', __DIR__ );

// Check if Composer is installed (remove if Composer is not required for your plugin).
if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	\add_action(
		'admin_notices',
		function() {
			?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'Composer is not installed and newsletter-builder cannot load. Try using a `*-built` branch if the plugin is being loaded as a submodule.', 'newsletter-builder' ); ?></p>
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

/**
 * Instantiate the plugin.
 */
function main() {
	new Ads();
	new Breaking_Recipients();
	Campaign_Monitor_Client::instance()->setup();
	new Email_Types();
	new Media();
	new Newsletter_Builder();
	new Rest_API_Endpoints();
	new Rest_API_Fields();
	new Rest_API_Query();
}
main();
