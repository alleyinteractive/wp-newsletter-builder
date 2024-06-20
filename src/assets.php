<?php
/**
 * Contains functions for working with assets (primarily JavaScript).
 *
 * phpcs:disable phpcs:ignore Squiz.PHP.CommentedOutCode.Found
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

// Register and enqueue assets.
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\action_wp_enqueue_scripts' );
add_action( 'wp_newsletter_builder_enqueue_styles', __NAMESPACE__ . '\action_newsletters_enqueue_styles' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\action_admin_enqueue_scripts' );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\action_enqueue_block_editor_assets' );
add_action( 'admin_head', __NAMESPACE__ . '\set_template_values' );

/**
 * A callback for the wp_enqueue_scripts hook.
 */
function action_wp_enqueue_scripts(): void {
	/*
	|--------------------------------------------------------------------------
	| Enqueue site assets using the asset/entry helper functions.
	|--------------------------------------------------------------------------
	|
	| This function is called by the enqueue_block_editor_assets hook. Use it to
	| enqueue assets that are loaded across your site.
	|
	| For more advanced usage, check out the WordPress Asset Manager plugin by Alley.
	|
	|     https://github.com/alleyinteractive/wp-asset-manager
	|
	*/
}

/**
 * A callback for the wp_newsletter_builder_enqueue_styles hook.
 */
function action_newsletters_enqueue_styles(): void {
	$blocks = [
		'button',
		'divider',
		'footer',
		'header',
		'heading',
		'list',
		'paragraph',
		'post',
		'section',
		'two-up-post',
	];
	?>
	<style type="text/css">
	<?php
	$template_id = get_post_meta( get_queried_object_id(), 'nb_newsletter_template', true );
	$font = get_post_meta( $template_id, 'nb_template_font', true );
	foreach ( $blocks as $block ) {
		if ( validate_path( trailingslashit( get_entry_dir_path( $block, true ) ) . 'style-index.css' ) ) {
			$entry_base_url = trailingslashit( get_entry_dir_path( $block, true ) ) . 'style-index.css';

			$css = file_get_contents( $entry_base_url ); // phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
			if ( ! empty( $css ) ) {
				$css = str_replace( '../images/', plugins_url( '../build/images/', __FILE__ ), $css );
				$css = str_replace( 'var(--test)', $font, $css );
				echo wp_strip_all_tags( $css ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}

	// Global block styles.
	if ( validate_path( trailingslashit( get_entry_dir_path( 'blocks', true ) ) . 'index.css' ) ) {
		$entry_base_url = trailingslashit( get_entry_dir_path( 'blocks', true ) ) . 'index.css';

		$css = file_get_contents( $entry_base_url ); // phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		if ( ! empty( $css ) ) {
			$css = str_replace( 'var(--test)', $font, $css );
			echo wp_strip_all_tags( $css ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
	?>
	</style>
	<?php
}

/**
 * A callback for the admin_enqueue_scripts hook.
 */
function action_admin_enqueue_scripts(): void {
	/*
	|--------------------------------------------------------------------------
	| Enqueue admin assets using the asset/entry helper functions.
	|--------------------------------------------------------------------------
	|
	| This function is called by the admin_enqueue_scripts hook. Use it to enqueue
	| assets that are loaded only in the WordPress admin.
	|
	*/
}

/**
 * A callback for the enqueue_block_editor_assets hook.
 */
function action_enqueue_block_editor_assets(): void {
	/*
	|--------------------------------------------------------------------------
	| Enqueue block editor assets using the asset/entry helper functions.
	|--------------------------------------------------------------------------
	|
	| This function is called by the enqueue_block_editor_assets hook. Use it to
	| enqueue assets that are loaded in the block editor.
	|
	*/

	global $post_type;

	// Only enqueue the script to register the scripts if supported.
	if ( 'nb_newsletter' !== $post_type && 'nb_template' !== $post_type ) {
		return;
	}

	$templates    = get_posts( // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
		[
			'post_type'        => 'nb_template',
			'posts_per_page'   => -1,
			'orderby'          => 'ID',
			'suppress_filters' => false,
		]
	);
	$template_map = [];

	foreach ( $templates as $template ) {
		$template_map[ $template->ID ] = $template->post_title;
	}
	wp_enqueue_style(
		'wp-newsletter-builder-editor',
		get_entry_asset_url( 'editor', 'index.css' ),
		get_asset_dependency_array( 'editor' ),
		get_asset_version( 'editor' )
	);
	$settings = new Settings();
	global $newsletter_builder_email_provider;
	$uses_suppression_lists = false;

	if ( ! empty( $newsletter_builder_email_provider ) && $newsletter_builder_email_provider instanceof Email_Providers\Email_Provider ) {
		$uses_suppression_lists = $newsletter_builder_email_provider->uses_suppression_lists();
	}

	/**
	 * Allow filtering of allowed post types available in the post picker.
	 *
	 * @param array<string> $allowed_post_types The allowed post types. Defaults to `post`.
	 * @return array<string> The filtered array of allowed post types.
	 */
	$allowed_post_types = apply_filters( 'wp_newsletter_builder_allowed_post_types', [ 'post' ] );

	wp_localize_script(
		'wp-newsletter-builder-email-settings-editor-script',
		'newsletterBuilder',
		[
			'fromNames'            => $settings->get_from_names(),
			'templates'            => $template_map,
			'usesSuppressionLists' => $uses_suppression_lists,
			'allowedPostTypes'     => $allowed_post_types,
		]
	);
}

/**
 * Validate file paths to prevent a PHP error if a file doesn't exist.
 *
 * @param string $path The file path to validate.
 * @return bool        True if the path is valid and the file exists.
 */
function validate_path( string $path ): bool {
	return 0 === validate_file( $path ) && file_exists( $path );
}

/**
 * Get the entry points directory path or public URL.
 *
 * @param string $dir_entry_name The directory name where the entry point was defined.
 * @param bool   $dir            Optional. Whether to return the directory path or the plugin URL path. Defaults to false (returns URL).
 *
 * @return string
 */
function get_entry_dir_path( string $dir_entry_name, bool $dir = false ): string {
	// The relative path from the plugin root.
	$asset_build_dir = "/build/{$dir_entry_name}/";
	// Set the absolute file path from the root directory.
	$asset_dir_path = WP_NEWSLETTER_BUILDER_DIR . $asset_build_dir;

	if ( validate_path( $asset_dir_path ) ) {
		// Negotiate the base path.
		return true === $dir
			? $asset_dir_path
			: plugins_url( $asset_build_dir, __DIR__ );
	}

	return '';
}

/**
 * Get the assets dependencies and version.
 *
 * @param string $dir_entry_name The entry point directory name.
 *
 * @return array{dependencies?: array<string>, version?: string}    An array of dependencies and version for this asset.
 */
function get_entry_asset_map( string $dir_entry_name ): array {
	$base_path = get_entry_dir_path( $dir_entry_name, true );

	if ( ! empty( $base_path ) ) {
		$asset_file_path = trailingslashit( $base_path ) . 'index.asset.php';

		if ( validate_path( $asset_file_path ) ) {
			return include $asset_file_path; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.IncludingFile, WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		}
	}

	return [];
}

/**
 * Get the dependency array for a given asset.
 *
 * @param string $dir_entry_name The entry point directory name.
 *
 * @return array<string> The asset's dependency array.
 */
function get_asset_dependency_array( string $dir_entry_name ): array {
	$asset_arr = get_entry_asset_map( $dir_entry_name );
	return $asset_arr['dependencies'] ?? [];
}

/**
 * Get the version hash for a given asset.
 *
 * @param string $dir_entry_name The entry point directory name.
 *
 * @return string The asset's version hash.
 */
function get_asset_version( string $dir_entry_name ): string {
	$asset_arr = get_entry_asset_map( $dir_entry_name );
	return $asset_arr['version'] ?? '1.0';
}

/**
 * Get the public url for the assets entry file.
 *
 * @param string $dir_entry_name The entry point directory name.
 * @param string $filename       The asset file name including the file type extension to get the public path for.
 * @return string                The public URL to the asset, empty string otherwise.
 */
function get_entry_asset_url( string $dir_entry_name, $filename = 'index.js' ): string {
	if ( empty( $filename ) ) {
		return '';
	}

	if ( validate_path( trailingslashit( get_entry_dir_path( $dir_entry_name, true ) ) . $filename ) ) {
		$entry_base_url = get_entry_dir_path( $dir_entry_name );

		if ( ! empty( $entry_base_url ) ) {
			return trailingslashit( $entry_base_url ) . $filename;
		}
	}

	return '';
}

/**
 * Load the php index files from the build directory for blocks, slotfills, and any other scripts with an index.php file.
 */
function load_scripts(): void {
	$paths = glob( WP_NEWSLETTER_BUILDER_DIR . '/build/**/index.php' );
	if ( ! empty( $paths ) ) {
		foreach ( $paths as $path ) {
			if ( 0 === validate_file( $path ) && file_exists( $path ) ) {
				require_once $path;  // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.IncludingFile, WordPressVIPMinimum.Files.IncludingFile.UsingVariable
			}
		}
	}
}

load_scripts();

/**
 * Set the template values for the editor.
 */
function set_template_values(): void {
	$current_screen = get_current_screen();
	if ( ( 'post' !== $current_screen->base ) || ( 'nb_newsletter' !== $current_screen->post_type ) ) {
		return;
	}
	$template_id = get_post_meta( get_the_ID(), 'nb_newsletter_template', true );
	$font = get_post_meta( $template_id, 'nb_template_font', true );
	echo("<style>:root {--test: {$font};}</style>");
}
