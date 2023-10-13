/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { ImagePicker, PostPicker } from '@alleyinteractive/block-editor-tools';
import { useSelect } from '@wordpress/data';
import {
  Button,
  CheckboxControl,
  PanelBody,
  PanelRow,
  Spinner,
} from '@wordpress/components';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';

import PostPickerResult from '@/components/postPickerResult';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './index.scss';

interface EditProps {
  attributes: {
    postId: number;
    showImage?: boolean;
    showExcerpt?: boolean;
    showContent?: boolean;
    showByline?: boolean;
    showCta?: boolean;
    order: string[];
    overrideTitle?: string;
    overrideImage?: number;
    overrideExcerpt?: string;
    overrideContent?: string;
    overrideByline?: string;
    number?: number;
    smallerFont?: boolean;
  },
  setAttributes: (attributes: {}) => void;
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
    postId = 0,
    showImage,
    showExcerpt,
    showContent,
    showCta,
    showByline,
    order,
    overrideTitle,
    overrideImage,
    overrideExcerpt,
    overrideContent,
    overrideByline,
    number,
    smallerFont,
  },
  setAttributes,
}: EditProps) {
  const handleSelect = (selected: number) => {
    setAttributes({
      postId: selected,
      overrideTitle: '',
      overrideImage: 0,
      overrideExcerpt: '',
      overrideContent: '',
    });
  };

  const removeLinks = (html: string) => (
    html ? html.replace(/<a[^>]*?>(.*?)<\/a>/gi, '$1') : ''
  );

  // @ts-ignore
  const record = useSelect((select) => (
    // @ts-ignore
    postId ? select('core').getEntityRecord('postType', 'post', postId) : null
  ), [postId]);

  let postTitle = record ? record.title.rendered : '';
  const postContent = record ? record.content.rendered : '';
  let postExcerpt = record ? record.excerpt.raw : '';
  let postByline = record ? record.wp_newsletter_builder_byline : '';
  let featuredMediaId = record ? record.featured_media : null;

  postTitle = overrideTitle || postTitle;
  featuredMediaId = overrideImage || featuredMediaId;
  postExcerpt = overrideExcerpt || postExcerpt;
  postByline = overrideByline || postByline;

  const {
    media = null,
  } = useSelect((select) => ({
    // @ts-ignore
    media: featuredMediaId ? select('core').getMedia(featuredMediaId) : null,
  }), [featuredMediaId]);

  const postImage = media ? media.source_url : '';

  const paragraphs = postContent.match(/<p(.*?)<\/p>/gi) || [];
  const content = overrideContent || removeLinks(paragraphs?.slice(0, 2).join(''));

  const cutoff = new Date();
  cutoff.setMonth(cutoff.getMonth() - 3);

  const titleClass = smallerFont ? 'post__title--small' : '';

  return (
    <div {...useBlockProps()}>
      {postId && !record ? (
        // @ts-ignore
        <Spinner />
      ) : null}
      {postId ? (
        <>
          {/* @ts-ignore */}
          <Button
            variant="primary"
            isDestructive
            onClick={() => handleSelect(0)}
            className="newsletter-remove-post"
            aria-label={__('Remove Post', 'wp-newsletter-builder')}
          >
            X
          </Button>
          {order.map((item) => {
            switch (item) {
              case 'image':
                return (
                  showImage && postImage ? (
                    <div className="image-container">
                      <img src={postImage} alt={postTitle} />
                    </div>
                  ) : null
                );
              case 'title':
                return (
                  <h2 className={titleClass}>
                    {number ? (
                      <span className="newsletter-post__number">
                        {`${number}. `}
                      </span>
                    ) : ''}
                    <RichText
                      value={postTitle}
                      tagName="span"
                      onChange={(value) => setAttributes({ overrideTitle: value })}
                    />
                  </h2>
                );
              case 'byline':
                return (
                  showByline ? (
                    <p className="post__byline">
                      {__('By ', 'wp-newsletter-builder')}
                      <RichText
                        value={postByline}
                        tagName="span"
                        onChange={(value) => setAttributes({ overrideByline: value })}
                        allowedFormats={[]}
                      />
                    </p>
                  ) : null
                );
              case 'excerpt':
                return (
                  showExcerpt ? (
                    <div className="post__dek">
                      <RichText
                        value={postExcerpt}
                        tagName="p"
                        multiline={false}
                        onChange={(value) => setAttributes({ overrideExcerpt: value })}
                      />
                    </div>
                  ) : null
                );
              case 'content':
                return (
                  showContent ? (
                    <RichText
                      value={content}
                      tagName="div"
                      multiline
                      onChange={(value) => setAttributes({ overrideContent: value })}
                    />
                  ) : null
                );
              case 'cta':
                return (
                  showCta ? (
                    <div className="newsletter-read-more has-text-align-center">
                      <span className="wp-element-button">
                        {__('Read More', 'wp-newsletter-builder')}
                      </span>
                    </div>
                  ) : null
                );
              default:
                return '';
            }
          })}
          <InspectorControls>
            {/* @ts-ignore */}
            <PanelBody
              title={__('Post', 'wp-newsletter-builder')}
              initialOpen
            >
              {/* @ts-ignore */}
              <PanelRow>
                {/* @ts-ignore */}
                <CheckboxControl
                  label={__('Show image', 'wp-newsletter-builder')}
                  checked={showImage}
                  onChange={(value) => setAttributes({ showImage: value })}
                />
              </PanelRow>
              {/* @ts-ignore */}
              <PanelRow>
                {/* @ts-ignore */}
                <CheckboxControl
                  label={__('Show Byline', 'wp-newsletter-builder')}
                  checked={showByline}
                  onChange={(value) => setAttributes({ showByline: value })}
                />
              </PanelRow>
              {/* @ts-ignore */}
              <PanelRow>
                {/* @ts-ignore */}
                <CheckboxControl
                  label={__('Show dek', 'wp-newsletter-builder')}
                  checked={showExcerpt}
                  onChange={(value) => setAttributes({ showExcerpt: value })}
                />
              </PanelRow>
              {/* @ts-ignore */}
              <PanelRow>
                {/* @ts-ignore */}
                <CheckboxControl
                  label={__('Show content', 'wp-newsletter-builder')}
                  checked={showContent}
                  onChange={(value) => setAttributes({ showContent: value })}
                />
              </PanelRow>
              {/* @ts-ignore */}
              <PanelRow>
                {/* @ts-ignore */}
                <CheckboxControl
                  label={__('Show CTA', 'wp-newsletter-builder')}
                  checked={showCta}
                  onChange={(value) => setAttributes({ showCta: value })}
                />
              </PanelRow>
            </PanelBody>
            {/* @ts-ignore */}
            <PanelBody
              title={__('Override Image', 'wp-newsletter-builder')}
              initialOpen
            >
              {/* @ts-ignore */}
              <PanelRow>
                <ImagePicker
                  value={overrideImage ?? 0}
                  onUpdate={({ id }) => setAttributes({ overrideImage: id })}
                  onReset={() => setAttributes({ overrideImage: 0 })}
                />
              </PanelRow>
            </PanelBody>
          </InspectorControls>
        </>
      ) : (
        <div>
          {/* @ts-ignore */}
          <PostPicker
            onUpdate={handleSelect}
            allowedTypes={['post']}
            onReset={() => handleSelect(0)}
            params={{ after: cutoff.toISOString(), per_page: 20 }}
            title={__('Please select a post', 'wp-newsletter-builder')}
            value={postId}
            // @ts-ignore
            searchRender={PostPickerResult}
          />
        </div>
      )}
    </div>
  );
}
