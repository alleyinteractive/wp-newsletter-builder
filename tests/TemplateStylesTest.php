<?php
/**
 * Template styles tests.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

use WP_Newsletter_Builder\Tests\Test_Case;

/**
 * Tests for sanitizing a template's font and colors, which are output in CSS.
 */
class TemplateStylesTest extends Test_Case {
	/**
	 * The template variables for a template with no valid font or colors.
	 *
	 * @var string
	 */
	private const DEFAULT_VARIABLES = ':root {--template-font-family: Arial, sans-serif; --template-bg-color: #fefefe; --template-link-color: #0073aa;}';

	public function test_valid_colors_are_kept() {
		$this->assertSame( '#ff0000', sanitize_template_color( '#ff0000' ) );
		$this->assertSame( '#f00', sanitize_template_color( '#f00' ) );
	}

	public function test_invalid_colors_are_removed() {
		$this->assertSame( '', sanitize_template_color( 'red' ) );
		$this->assertSame( '', sanitize_template_color( '#fff;} body {display: none' ) );
		$this->assertSame( '', sanitize_template_color( [ '#fff' ] ) );
	}

	public function test_valid_fonts_are_kept() {
		$this->assertSame( 'Times New Roman, serif', sanitize_template_font( 'Times New Roman, serif' ) );
		$this->assertSame( 'Lucida Sans Unicode, sans-serif', sanitize_template_font( 'Lucida Sans Unicode, sans-serif' ) );
	}

	public function test_invalid_fonts_are_removed() {
		$this->assertSame( '', sanitize_template_font( 'Arial;} @import url(https://example.com/x.css);' ) );
		$this->assertSame( '', sanitize_template_font( '</style><script>alert(1)</script>' ) );
		$this->assertSame( '', sanitize_template_font( null ) );
	}

	public function test_meta_is_sanitized_on_save() {
		$template_id = static::factory()->post->create( [ 'post_type' => 'nb_template' ] );

		update_post_meta( $template_id, 'nb_template_font', '</style><script>alert(1)</script>' );
		update_post_meta( $template_id, 'nb_template_bg_color', '#fff;}' );
		update_post_meta( $template_id, 'nb_template_link_color', '#ff0000' );

		$this->assertSame( '', get_post_meta( $template_id, 'nb_template_font', true ) );
		$this->assertSame( '', get_post_meta( $template_id, 'nb_template_bg_color', true ) );
		$this->assertSame( '#ff0000', get_post_meta( $template_id, 'nb_template_link_color', true ) );
	}

	public function test_invalid_saved_values_are_not_output() {
		// Simulate values saved before the meta was sanitized.
		remove_all_filters( 'sanitize_post_meta_nb_template_font_for_nb_template' );
		remove_all_filters( 'sanitize_post_meta_nb_template_bg_color_for_nb_template' );

		$template_id = static::factory()->post->create(
			[
				'post_type'  => 'nb_template',
				'meta_input' => [
					'nb_template_font'     => '</style><script>alert(1)</script>',
					'nb_template_bg_color' => '#fff;} body {display: none',
				],
			]
		);

		$this->assertSame( self::DEFAULT_VARIABLES, get_template_variables_css( $template_id ) );
	}
}
