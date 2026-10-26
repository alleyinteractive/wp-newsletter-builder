<?php
/**
 * Contains functions for working with meta.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

// Register custom meta fields.
register_post_meta_from_defs();

/**
 * Register meta for posts or terms with sensible defaults and sanitization.
 *
 * @throws \InvalidArgumentException For unmet requirements.
 *
 * @see \register_post_meta
 * @see \register_term_meta
 *
 * @param string               $object_type  The type of meta to register, which must be one of 'post' or 'term'.
 * @param array<string>        $object_slugs The post type or taxonomy slugs to register with.
 * @param string               $meta_key     The meta key to register.
 * @param array<string, mixed> $args         Optional. Additional arguments for register_post_meta or register_term_meta. Defaults to an empty array.
 * @return bool      True if the meta key was successfully registered in the global array, false if not.
 */
function register_meta_helper(
	string $object_type,
	array $object_slugs,
	string $meta_key,
	array $args = []
): bool {

	// Object type must be either post or term.
	if ( ! in_array( $object_type, [ 'post', 'term' ], true ) ) {
		throw new \InvalidArgumentException(
			esc_html__(
				'Object type must be one of "post", "term".',
				'wp-newsletter-builder'
			)
		);
	}

	/**
	 * Merge provided arguments with defaults and filter register_meta() args.
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_meta/
	 *
	 * @param array<string, mixed> $args {
	 *     Array of args to be passed to register_meta().
	 *
	 *     @type string     $object_subtype    A subtype; e.g. if the object type is "post", the post type. If left empty,
	 *                                         the meta key will be registered on the entire object type. Default empty.
	 *     @type string     $type              The type of data associated with this meta key. Valid values are
	 *                                         'string', 'boolean', 'integer', 'number', 'array', and 'object'.
	 *     @type string     $description       A description of the data attached to this meta key.
	 *     @type bool       $single            Whether the meta key has one value per object, or an array of values per object.
	 *     @type mixed      $default           The default value returned from get_metadata() if no value has been set yet.
	 *                                         When using a non-single meta key, the default value is for the first entry. In other words,
	 *                                         when calling get_metadata() with $single set to false, the default value given here will be wrapped in an array.
	 *     @type callable   $sanitize_callback A function or method to call when sanitizing $meta_key data.
	 *     @type callable   $auth_callback     Optional. A function or method to call when performing edit_post_meta,
	 *                                         add_post_meta, and delete_post_meta capability checks.
	 *     @type bool|array $show_in_rest      Whether data associated with this meta key can be considered public and should be
	 *                                         accessible via the REST API. A custom post type must also declare support
	 *                                         for custom fields for registered meta to be accessible via REST. When registering
	 *                                         complex meta values this argument may optionally be an array with 'schema'
	 *                                         or 'prepare_callback' keys instead of a boolean.
	 * }
	 * @param string $object_type  The type of meta to register, which must be one of 'post' or 'term'.
	 * @param array  $object_slugs The post type or taxonomy slugs to register with.
	 * @param string $meta_key     The meta key to register.
	 */
	$args = apply_filters(
		'wp_newsletter_builder_register_meta_helper_args', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		array_merge(
			[
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			],
			$args
		),
		$object_type,
		$object_slugs,
		$meta_key
	);

	$args = normalize_meta_args( $args );

	// Fork for object type.
	switch ( $object_type ) {
		case 'post':
			foreach ( $object_slugs as $object_slug ) {
				if ( ! register_post_meta( $object_slug, $meta_key, $args ) ) {
					return false;
				}
			}
			break;
		case 'term':
			foreach ( $object_slugs as $object_slug ) {
				if ( ! register_term_meta( $object_slug, $meta_key, $args ) ) {
					return false;
				}
			}
			break;
		default:
			return false;
	}

	return true;
}

/**
 * Normalizes register_meta() arguments to the types WordPress expects.
 *
 * Unknown keys are dropped (register_meta() ignores them anyway), as are known keys whose values have the wrong type.
 *
 * @param array<string, mixed> $args Arguments to normalize.
 * @return array{
 *   object_subtype?: string,
 *   type?: string,
 *   label?: string,
 *   description?: string,
 *   single?: bool,
 *   default?: mixed,
 *   sanitize_callback?: callable,
 *   auth_callback?: callable,
 *   show_in_rest?: bool|array<mixed>,
 *   revisions_enabled?: bool,
 * } The normalized arguments.
 */
function normalize_meta_args( array $args ): array {
	$normalized = [];

	if ( isset( $args['object_subtype'] ) && is_string( $args['object_subtype'] ) ) {
		$normalized['object_subtype'] = $args['object_subtype'];
	}
	if ( isset( $args['type'] ) && is_string( $args['type'] ) ) {
		$normalized['type'] = $args['type'];
	}
	if ( isset( $args['label'] ) && is_string( $args['label'] ) ) {
		$normalized['label'] = $args['label'];
	}
	if ( isset( $args['description'] ) && is_string( $args['description'] ) ) {
		$normalized['description'] = $args['description'];
	}
	if ( isset( $args['single'] ) && is_bool( $args['single'] ) ) {
		$normalized['single'] = $args['single'];
	}
	if ( array_key_exists( 'default', $args ) ) {
		$normalized['default'] = $args['default'];
	}
	if ( isset( $args['sanitize_callback'] ) && is_callable( $args['sanitize_callback'] ) ) {
		$normalized['sanitize_callback'] = $args['sanitize_callback'];
	}
	if ( isset( $args['auth_callback'] ) && is_callable( $args['auth_callback'] ) ) {
		$normalized['auth_callback'] = $args['auth_callback'];
	}
	if ( isset( $args['show_in_rest'] ) && ( is_bool( $args['show_in_rest'] ) || is_array( $args['show_in_rest'] ) ) ) {
		$normalized['show_in_rest'] = $args['show_in_rest'];
	}
	if ( isset( $args['revisions_enabled'] ) && is_bool( $args['revisions_enabled'] ) ) {
		$normalized['revisions_enabled'] = $args['revisions_enabled'];
	}

	return $normalized;
}

/**
 * Reads the post meta definitions from config and registers them.
 */
function register_post_meta_from_defs(): void {
	// Ensure the config file exists.
	$filepath = dirname( __DIR__ ) . '/config/post-meta.json';
	if ( ! file_exists( $filepath )
		|| 0 !== validate_file( $filepath )
	) {
		return;
	}

	// Try to read the file's contents. We can dismiss the "uncached" warning here because it is a local file.
	// phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
	$file_contents = file_get_contents( $filepath );
	if ( empty( $file_contents ) ) {
		return;
	}

	$definitions = json_decode( $file_contents, true );
	if ( empty( $definitions ) || ! is_array( $definitions ) ) {
		return;
	}

	// Loop through definitions and register each.
	foreach ( $definitions as $meta_key => $definition ) {
		if ( ! is_string( $meta_key ) || ! is_array( $definition ) ) {
			continue;
		}

		// Only string keys are valid register_meta args.
		$definition = array_filter( $definition, 'is_string', ARRAY_FILTER_USE_KEY );

		// Extract post types.
		$post_types = isset( $definition['post_types'] ) && is_array( $definition['post_types'] )
			? array_values( array_filter( $definition['post_types'], 'is_string' ) )
			: [];
		// Unset since $definition is passed as register_meta args.
		unset( $definition['post_types'] );

		// Relocate schema, if specified at the top level.
		if ( ! empty( $definition['schema'] ) ) {
			$show_in_rest               = isset( $definition['show_in_rest'] ) && is_array( $definition['show_in_rest'] ) ? $definition['show_in_rest'] : [];
			$show_in_rest['schema']     = $definition['schema'];
			$definition['show_in_rest'] = $show_in_rest;
			// Unset since $definition is passed as register_meta args.
			unset( $definition['schema'] );
		}

		// Register the meta.
		register_meta_helper(
			'post',
			$post_types,
			$meta_key,
			$definition
		);
	}
}
