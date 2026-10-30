<?php
/**
 * Null_Provider tests.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

use WP_Newsletter_Builder\Email_Providers\Null_Provider;
use WP_Newsletter_Builder\Tests\Test_Case;

/**
 * Tests for the Null email provider.
 */
class NullProviderTest extends Test_Case {
	/**
	 * The provider that was selected before each test.
	 *
	 * @var mixed
	 */
	private mixed $original_provider;

	protected function setUp(): void {
		parent::setUp();

		global $newsletter_builder_email_provider;
		$this->original_provider = $newsletter_builder_email_provider;
	}

	protected function tearDown(): void {
		global $newsletter_builder_email_provider;
		$newsletter_builder_email_provider = $this->original_provider;

		parent::tearDown();
	}

	public function test_provider_sends_email() {
		global $newsletter_builder_email_provider;

		$newsletter_builder_email_provider = null;
		$this->assertTrue( provider_sends_email() );

		$newsletter_builder_email_provider = new Null_Provider();
		$this->assertFalse( provider_sends_email() );
	}

	public function test_publishing_does_not_send_or_store_a_campaign() {
		global $newsletter_builder_email_provider;
		$newsletter_builder_email_provider = new Null_Provider();

		$this->prevent_stray_requests();

		$post_id = static::factory()->post->create(
			[
				'post_type'   => 'nb_newsletter',
				'post_status' => 'draft',
				'meta_input'  => [
					'nb_newsletter_list' => [ 'some-list' ],
				],
			]
		);
		wp_update_post(
			[
				'ID'          => $post_id,
				'post_status' => 'publish',
			]
		);

		$this->assertSame( 'publish', get_post_status( $post_id ) );
		$this->assertEmpty( get_post_meta( $post_id, 'nb_newsletter_campaign_id', true ) );
		$this->assertEmpty( get_post_meta( $post_id, 'nb_newsletter_send_result', true ) );
		$this->assertEmpty( get_post_meta( $post_id, 'nb_newsletter_campaign_result', true ) );
	}
}
