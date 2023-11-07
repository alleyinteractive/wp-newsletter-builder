<?php
/**
 * Ads class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Ads class
 */
class Ads {
	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'the_content', [ $this, 'insert_ads' ], 8, 1 );
		add_filter( 'render_block_wp-newsletter-builder/ad', [ $this, 'render_ad' ], 10, 2 );
		add_filter( 'wp_kses_allowed_html', [ $this, 'modify_allowed_html' ], 10, 2 );
		add_filter( 'the_content', [ $this, 'reinsert_variable' ], 999, 1 );
	}

	/**
	 * Inserts ads into the nb_newsletter post content.
	 *
	 * @param string $content The existing content.
	 * @return string The modified content.
	 */
	public function insert_ads( $content ): string {
		global $post;
		if ( 'nb_newsletter' !== $post->post_type ) {
			return $content;
		}

		$nb_email_type          = get_post_meta( $post->ID, 'nb_newsletter_email_type', true );
		$nb_list_ids            = get_post_meta( $post->ID, 'nb_newsletter_list', true ) ?? '';
		$nb_newsletter_template = get_post_meta( $post->ID, 'nb_newsletter_template', true ) ?? '';
		if ( is_array( $nb_list_ids ) ) {
			$nb_list_ids = implode( ',', $nb_list_ids );
		}

		$email_types_class = new \WP_Newsletter_Builder\Email_Types();
		$email_types       = $email_types_class->get_email_types();
		$matching_types    = array_filter(
			$email_types,
			function ( $email_type ) use ( $nb_email_type ) {
				return $email_type['uuid4'] === $nb_email_type;
			}
		);
		$match             = ! empty( $matching_types ) ? array_shift( $matching_types ) : [];

		$safe_rtb        = ! empty( $match ) ? $match['safe_rtb'] ?? '' : '';
		$ad_tags         = ! empty( $match ) ? $match['ad_tags'] ?? [] : [];
		$roadblock       = ! empty( $match ) ? $match['roadblock'] ?? false : false;
		$key_values      = ! empty( $match ) ? $match['key_values'] ?? [] : [];
		$key_value_array = [];
		foreach ( $key_values as $pair ) {
			$key                     = $pair['key'];
			$value                   = $pair['value'];
			$key_value_array[ $key ] = $value;
		}
		if ( $roadblock ) {
			$roadblock_number             = wp_rand( 1, 3 );
			$key_value_array['roadblock'] = "rb{$roadblock_number}";
		}
		$key_value_string = http_build_query( $key_value_array );

		$has_middle_ad = 2 < count( $ad_tags );

		$blocks = parse_blocks( $content );
		$rtb    = [
			'blockName'    => 'wp-newsletter-builder/ad',
			'attrs'        => [
				'adTag' => $this->replace_values( $safe_rtb, $key_value_string, $nb_list_ids ),
			],
			'innerContent' => [],
			'innerHtml'    => $safe_rtb,
		];
		$ad1    = [
			'blockName'    => 'wp-newsletter-builder/ad',
			'attrs'        => [
				'adTag' => $this->replace_values( $ad_tags[0]['tag_code'] ?? '', $key_value_string, $nb_list_ids ),
			],
			'innerContent' => [],
			'innerHtml'    => '',
		];

		$ad2 = [
			'blockName'    => 'wp-newsletter-builder/ad',
			'attrs'        => [
				'adTag' => $this->replace_values( $ad_tags[1]['tag_code'] ?? '', $key_value_string, $nb_list_ids ),
			],
			'innerContent' => [],
			'innerHtml'    => '',
		];
		if ( $has_middle_ad ) {
			$ad3 = [
				'blockName'    => 'wp-newsletter-builder/ad',
				'attrs'        => [
					'adTag' => $this->replace_values( $ad_tags[2]['tag_code'] ?? '', $key_value_string, $nb_list_ids ),
				],
				'innerContent' => [],
				'innerHtml'    => '',
			];
			if ( isset( $ad2['attrs']['adTag'] ) ) {
				$ad2['attrs']['adTag'] = '<hr class="wp-block-separator has-alpha-channel-opacity">' . $ad2['attrs']['adTag'];
			}
		}
		$ad_footer = [
			'blockName'    => 'wp-newsletter-builder/ad',
			'attrs'        => [
				'adTag' => '<table style="width:100%" class="liveintent-disclosures">
						<tr>
							<td style="border-top:1px solid #dcdcdc;padding:5px 0 7px;text-align:center">
								<a style="display:inline-block;text-decoration:none;color:#666;border-right:1px solid #dcdcdc;padding-right:10px;margin-right:10px;line-height:0" href="https://www.liveintent.com/powered-by/" target="_blank">
									<img width="150" alt="LiveIntent Logo" style="vertical-align:middle;" src="https://c.licasd.com/ads/14722e4924b411ed82660a76a0f6ca19/51057efc7872807594dc37bb448cb577.png">
								</a>
								<a style="display:inline-block;text-decoration:none;color:#666;line-height:0" href="https://www.liveintent.com/ad-choices/" color="#666666" target="_blank">
									<img width="75" alt="AdChoices Logo" style="vertical-align:middle;" src="https://c.licasd.com/ads/14722e4924b411ed82660a76a0f6ca19/0f3a8231b771f2e24858f02d9e98e4e5.png">
								</a>
							</td>
						</tr>
					</table>',
			],
			'innerContent' => [],
			'innerHtml'    => '',
		];
		array_splice( $blocks, 0, 0, [ $rtb, $ad1 ] );
		if ( 'single-story.html' === $nb_newsletter_template ) {
			$bottom_ad_location = self::get_bottom_ad_location( $blocks );
			$separator          = $blocks[ $bottom_ad_location ];
			array_splice( $blocks, $bottom_ad_location, 0, [ $separator, $has_middle_ad ? $ad3 : $ad2 ] );
		} else {
			$blocks = array_merge( $blocks, [ $has_middle_ad ? $ad3 : $ad2 ] );
		}
		if ( $has_middle_ad ) {
			$middle_ad_location = self::get_middle_ad_location( $blocks );
			if ( null !== $middle_ad_location ) {
				array_splice( $blocks, $middle_ad_location, 0, [ $ad2 ] );
			}
		}
		$blocks[] = $ad_footer;

		return serialize_blocks( $blocks );
	}

	/**
	 * Renders our fake block: wp-newsletter-builder/ad.
	 *
	 * @param string $content The existing content - should be empty.
	 * @param array  $block The block data.
	 * @return string
	 */
	public function render_ad( $content, $block ): string {
		$ad_tag = $block['attrs']['adTag'];
		$ad_tag = str_replace( '<$Enc.CampaignID$>', 'enc-campaign-id', $ad_tag );
		return wp_kses_post( sprintf( '<div class="wp-newsletter-builder__ad">%s</div>', $ad_tag ) );
	}

	/**
	 * Finds the insertion point for the middle ad.
	 *
	 * @param array $blocks The array of blocks.
	 * @return integer|null
	 */
	public function get_middle_ad_location( $blocks ): ?int {
		foreach ( $blocks as $index => $block ) {
			if (
				'wp-newsletter-builder/section' === $block['blockName']
				&& true === $block['attrs']['adAfter']
			) {
				return $index + 1;
			}
		}
		return null;
	}

	/**
	 * Finds the insertion point for the bottom ad.
	 *
	 * @param array $blocks The array of blocks.
	 * @return integer|null
	 */
	public function get_bottom_ad_location( $blocks ): ?int {
		foreach ( $blocks as $index => $block ) {
			if (
				'core/separator' === $block['blockName']
			) {
				return $index;
			}
		}
		return null;
	}

	/**
	 * Adds height to the allowed table attributes.
	 *
	 * @param array        $allowed_html The existing allowed html.
	 * @param string|array $context The context.
	 * @return array
	 */
	public function modify_allowed_html( $allowed_html, $context ) {
		if ( 'post' === $context ) {
			$allowed_html['table']['height']           = true;
			$allowed_html['img']['cm_dontimportimage'] = true;
		}
		return $allowed_html;
	}

	/**
	 * Replaces our temporary 'enc-campaign-id' placeholder with the real variable.
	 *
	 * @param string $content The content.
	 * @return string
	 */
	public function reinsert_variable( $content ) {
		return str_replace( 'enc-campaign-id', '<$Enc.CampaignID$>', $content );
	}

	/**
	 * Inserts the key values and listids into the ad tag.
	 *
	 * @param string $ad_tag The ad tag html code.
	 * @param string $key_value_string The key value string.
	 * @param string $list_ids The list ids.
	 * @return string
	 */
	public function replace_values( $ad_tag, $key_value_string = '', $list_ids = '' ) {
		$new_ad_tag = str_replace( '{LIST_ID}', $list_ids, $ad_tag );
		return preg_replace( '/(src|href)="([^"]*?)"/', "$1=\"$2&$key_value_string\"", $new_ad_tag );
	}
}
