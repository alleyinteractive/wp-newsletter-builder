/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import classNames from 'classnames';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

import apiFetch from '@wordpress/api-fetch';
import { Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './index.scss';

interface EditProps {
  attributes: {
    narrow_separator: boolean,
  };
}

interface FooterSettings {
  facebook_url: string,
  twitter_url: string,
  instagram_url: string,
  youtube_url: string,
  image: number,
  address: string,
}

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({
  attributes: {
    narrow_separator: narrowSeparator = false,
  },
}: EditProps) {
  const [isLoading, setIsLoading] = useState(true);
  const [footerSettings, setFooterSettings] = useState<FooterSettings>();

  const facebookUrl = footerSettings?.facebook_url ?? '';
  const twitterUrl = footerSettings?.twitter_url ?? '';
  const instagramUrl = footerSettings?.instagram_url ?? '';
  const youtubeUrl = footerSettings?.youtube_url ?? '';
  const imageId = footerSettings?.image ?? 0;
  const address = footerSettings?.address ?? '';

  useEffect(() => {
    if (footerSettings) {
      setIsLoading(false);
      return;
    }
    apiFetch({ path: '/newsletter-builder/v1/footer_settings' }).then((response) => {
      setFooterSettings(response as any as FooterSettings);
    });
  }, [footerSettings]);

  const {
    media = null,
  } = useSelect((select) => ({
    // @ts-ignore
    media: imageId ? select('core').getMedia(imageId) : null,
  }), [footerSettings, imageId]);

  const imageUrl = media ? media.source_url : '';
  const imageAltText = media ? media.alt_text : '';

  return (
    <div {...useBlockProps()}>
      <hr className={
        classNames('wp-block-separator', 'has-alpha-channel-opacity', { 'is-style-wide': !narrowSeparator })
      }
      />
      {isLoading
        ? (
          /* @ts-ignore */
          <Spinner />
        ) : (
          <>
            {facebookUrl || twitterUrl || instagramUrl || youtubeUrl
              ? (
                <div className="wp-block-newsletter-builder-footer__social-links">
                  {facebookUrl
                    ? (
                      <span className="wp-block-newsletter-builder-footer__social-links__item">
                        <a className="wp-block-newsletter-builder-footer__social-links__link" href={facebookUrl}>
                          <img src="/wp-content/plugins/newsletter-builder/images/facebook.png" alt="Facebook" height="26" width="26" />
                        </a>
                      </span>
                    ) : null}
                  {twitterUrl
                    ? (
                      <span className="wp-block-newsletter-builder-footer__social-links__item">
                        <a className="wp-block-newsletter-builder-footer__social-links__link" href={twitterUrl}>
                          <img src="/wp-content/plugins/newsletter-builder/images/twitter.png" alt="Twitter" height="26" width="26" />
                        </a>
                      </span>
                    ) : null}
                  {instagramUrl
                    ? (
                      <span className="wp-block-newsletter-builder-footer__social-links__item">
                        <a className="wp-block-newsletter-builder-footer__social-links__link" href={instagramUrl}>
                          <img src="/wp-content/plugins/newsletter-builder/images/instagram.png" alt="Instagram" height="26" width="26" />
                        </a>
                      </span>
                    ) : null}
                  {youtubeUrl
                    ? (
                      <span className="wp-block-newsletter-builder-footer__social-links__item">
                        <a className="wp-block-newsletter-builder-footer__social-links__link" href={youtubeUrl}>
                          <img src="/wp-content/plugins/newsletter-builder/images/youtube.png" alt="YouTube" height="26" width="26" />
                        </a>
                      </span>
                    ) : null}
                </div>
              ) : null}
            {imageUrl
              ? (
                <div className="wp-block-newsletter-builder-footer__logo">
                  <img src={imageUrl} alt={imageAltText} width="300" />
                </div>
              ) : null}
            {address
              ? (
                <div className="wp-block-newsletter-builder-footer__address">
                  {address}
                </div>
              )
              : null}
          </>
        )}
      <div className="wp-block-newsletter-builder-footer__links">
        {/* @ts-ignore */}
        <preferences>{__('Preferences', 'newsletter-builder')}</preferences>
        {' | '}
        {/* @ts-ignore */}
        <unsubscribe>{__('Unsubscribe', 'newsletter-builder')}</unsubscribe>
      </div>
    </div>
  );
}
