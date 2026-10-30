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
	 * Gets the filtered editor styles for a post of a post type.
	 *
	 * @param string $post_type The post type.
	 * @return array<array<string, mixed>>
	 */
	private function get_editor_styles( string $post_type ): array {
		$post = static::factory()->post->create_and_get( [ 'post_type' => $post_type ] );

		return filter_newsletter_editor_styles(
			[ 'styles' => self::STYLES ],
			new WP_Block_Editor_Context( [ 'post' => $post ] )
		)['styles'];
	}

	/**
	 * Gets the CSS of the filtered editor styles for a post of a post type.
	 *
	 * @param string $post_type The post type.
	 * @return array<string>
	 */
	private function get_editor_css( string $post_type ): array {
		return array_column( $this->get_editor_styles( $post_type ), 'css' );
	}

	public function test_filter_is_hooked() {
		$this->assertSame( 10, has_filter( 'block_editor_settings_all', __NAMESPACE__ . '\filter_newsletter_editor_styles' ) );
	}

	public function test_theme_styles_are_removed_from_newsletters_and_templates() {
		$expected = [ '/* variables */', '/* duotone */' ];

		$this->assertSame( $expected, $this->get_editor_css( 'nb_newsletter' ) );
		$this->assertSame( $expected, $this->get_editor_css( 'nb_template' ) );
	}

	public function test_theme_styles_are_kept_for_other_post_types() {
		$this->assertSame( array_column( self::STYLES, 'css' ), $this->get_editor_css( 'post' ) );
	}

	public function test_theme_styles_can_be_kept() {
		add_filter( 'wp_newsletter_builder_remove_theme_editor_styles', '__return_false' );

		$this->assertSame( array_column( self::STYLES, 'css' ), $this->get_editor_css( 'nb_newsletter' ) );
	}

	public function test_newsletter_styles_can_be_added() {
		add_filter(
			'wp_newsletter_builder_editor_styles',
			fn () => [
				[ 'css' => '/* newsletter */' ],
			]
		);

		$this->assertSame(
			[
				self::STYLES[0],
				self::STYLES[4],
				[
					'css'            => '/* newsletter */',
					'__unstableType' => 'user',
				],
			],
			$this->get_editor_styles( 'nb_newsletter' )
		);
	}
}
