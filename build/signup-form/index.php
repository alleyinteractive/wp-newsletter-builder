<?php
/**
 * Block Name: Newsletter Signup Form.
 *
 * @package wp-newsletter-builder
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function wp_newsletter_builder_signup_form_block_init(): void {
	// Register the block by passing the location of block.json.
	register_block_type(
		__DIR__
	);
}
add_action( 'init', 'wp_newsletter_builder_signup_form_block_init' );

/**
 * Allows the use of the input tag in the newsletter builder block.
 *
 * @param array<string, array<string, array<string, string>|bool>> $tags The allowed tags.
 * @return array<string, array<string, array<string, string>|bool>>
 */
function wp_newsletter_builder_modify_wpkses_post_allowed_tags( array $tags ): array {
	$tags['input'] = [
		'type'        => true,
		'name'        => true,
		'value'       => [],
		'placeholder' => true,
		'class'       => true,
		'checked'     => true,
	];
	return $tags;
}
add_filter( 'wp_kses_allowed_html', 'wp_newsletter_builder_modify_wpkses_post_allowed_tags' );
