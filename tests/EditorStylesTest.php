<?php
/**
 * Editor styles tests.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

use WP_Block_Editor_Context;
use WP_Newsletter_Builder\Tests\Test_Case;

/**
 * Tests for the styles in the newsletter and template editors.
 */
class EditorStylesTest extends Test_Case {
	/**
	 * Editor styles of each type, as core provides them.
	 *
	 * @var array<array<string, mixed>>
	 */
	private const STYLES = [
		[
			'css'            => '/* variables */',
			'__unstableType' => 'presets',
			'isGlobalStyles' => true,
		],
		[
			'css'            => '/* global styles */',
			'__unstableType' => 'theme',
			'isGlobalStyles' => true,
		],
		[
			'css'            => '/* additional css */',
			'__unstableType' => 'user',
			'isGlobalStyles' => false,
		],
		[
			'css'            => '/* editor stylesheet */',
			'__unstableType' => 'theme',
			'isGlobalStyles' => false,
		],
		[
			'css'            => '/* duotone */',
			'__unstableType' => 'svgs',
			'isGlobalStyles' => false,
		],
		[
			'css' => '/* untyped */',
		],
	];

	/**
	 * The template variables for a newsletter without a template.
	 *
	 * @var string
	 */
	private const DEFAULT_VARIABLES = ':root {--template-font-family: Arial, sans-serif; --template-bg-color: #fefefe; --template-link-color: #0073aa;}';

	/**
	 * Gets the filtered editor styles for a post.
	 *
	 * @param string|\WP_Post $post A post, or a post type to create one of.
	 * @return array<array<string, mixed>>
	 */
	private function get_editor_styles( string|\WP_Post $post ): array {
		if ( is_string( $post ) ) {
			$post = static::factory()->post->create_and_get( [ 'post_type' => $post ] );
		}

		return filter_newsletter_editor_styles(
			[ 'styles' => self::STYLES ],
			new WP_Block_Editor_Context( [ 'post' => $post ] )
		)['styles'];
	}

	/**
	 * Gets the CSS of the filtered editor styles for a post.
	 *
	 * @param string|\WP_Post $post A post, or a post type to create one of.
	 * @return array<string>
	 */
	private function get_editor_css( string|\WP_Post $post ): array {
		return array_column( $this->get_editor_styles( $post ), 'css' );
	}

	/**
	 * Creates a template with a font and colors.
	 *
	 * @return int The template ID.
	 */
	private function create_template(): int {
		return static::factory()->post->create(
			[
				'post_type'  => 'nb_template',
				'meta_input' => [
					'nb_template_font'       => 'Georgia, serif',
					'nb_template_bg_color'   => '#eeeeee',
					'nb_template_link_color' => '#ff0000',
				],
			]
		);
	}

	public function test_filter_is_hooked() {
		$this->assertSame( 10, has_filter( 'block_editor_settings_all', __NAMESPACE__ . '\filter_newsletter_editor_styles' ) );
	}

	public function test_theme_styles_are_removed_from_newsletters_and_templates() {
		$expected = [ '/* variables */', '/* duotone */', self::DEFAULT_VARIABLES ];

		$this->assertSame( $expected, $this->get_editor_css( 'nb_newsletter' ) );
		$this->assertSame( $expected, $this->get_editor_css( 'nb_template' ) );
	}

	public function test_theme_styles_are_kept_for_other_post_types() {
		$this->assertSame( array_column( self::STYLES, 'css' ), $this->get_editor_css( 'post' ) );
	}

	public function test_theme_styles_can_be_kept() {
		add_filter( 'wp_newsletter_builder_remove_theme_editor_styles', '__return_false' );

		$this->assertSame(
			[ ...array_column( self::STYLES, 'css' ), self::DEFAULT_VARIABLES ],
			$this->get_editor_css( 'nb_newsletter' )
		);
	}

	public function test_template_variables_are_added_for_templates() {
		$expected = ':root {--template-font-family: Georgia, serif; --template-bg-color: #eeeeee; --template-link-color: #ff0000;}';

		$this->assertSame(
			[
				'css'            => $expected,
				'id'             => 'wp-newsletter-builder-template-variables',
				'__unstableType' => 'user',
			],
			$this->get_editor_styles( get_post( $this->create_template() ) )[2]
		);
	}

	public function test_template_variables_are_added_for_newsletters_from_their_template() {
		$newsletter = static::factory()->post->create_and_get(
			[
				'post_type'  => 'nb_newsletter',
				'meta_input' => [ 'nb_newsletter_template' => $this->create_template() ],
			]
		);

		$this->assertSame(
			':root {--template-font-family: Georgia, serif; --template-bg-color: #eeeeee; --template-link-color: #ff0000;}',
			$this->get_editor_css( $newsletter )[2]
		);
	}

	public function test_newsletter_styles_can_be_added() {
		add_filter(
			'wp_newsletter_builder_editor_styles',
			fn () => [
				[ 'css' => '/* newsletter */' ],
			]
		);

		$this->assertSame(
			[ '/* variables */', '/* duotone */', self::DEFAULT_VARIABLES, '/* newsletter */' ],
			$this->get_editor_css( 'nb_newsletter' )
		);
		$this->assertSame( 'user', $this->get_editor_styles( 'nb_newsletter' )[3]['__unstableType'] );
	}
}
