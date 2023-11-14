/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __, sprintf } from '@wordpress/i18n';
import { ImagePicker, PostPicker } from '@alleyinteractive/block-editor-tools';
import { useSelect } from '@wordpress/data';
import {
  Button,
  CheckboxControl,
  PanelBody,
  PanelRow,
  Spinner,
  TextControl,
} from '@wordpress/components';
import { arrayMoveImmutable } from 'array-move';

import SortableList, { SortableItem, SortableKnob } from 'react-easy-sort';

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
    overrideUrl?: string,
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
    overrideUrl,
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
      overrideUrl: '',
    });
  };

  const isShown = (item: string) => {
    switch (item) {
      case 'image':
        return showImage;
      case 'title':
        return true;
      case 'byline':
        return showByline;
      case 'excerpt':
        return showExcerpt;
      case 'content':
        return showContent;
      case 'cta':
        return showCta;
      default:
        return false;
    }
  };

  const toggleShown = (item: string) => {
    switch (item) {
      case 'image':
        setAttributes({ showImage: !showImage });
        break;
      case 'byline':
        setAttributes({ showByline: !showByline });
        break;
      case 'excerpt':
        setAttributes({ showExcerpt: !showExcerpt });
        break;
      case 'content':
        setAttributes({ showContent: !showContent });
        break;
      case 'cta':
        setAttributes({ showCta: !showCta });
        break;
      default:
        break;
    }
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

  const onSortEnd = (oldIndex: number, newIndex: number) => {
    const newOrder = arrayMoveImmutable(
      [...order as string[]], // eslint-disable-line camelcase
      oldIndex,
      newIndex,
    );
    setAttributes({ order: newOrder });
  };

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
      <InspectorControls>
        {/* @ts-ignore */}
        <PanelBody
          title={__('Post', 'wp-newsletter-builder')}
          initialOpen
        >
          <PanelRow>
            <SortableList
              onSortEnd={onSortEnd}
              className="sortable"
              lockAxis="y"
            >
              {order.map((item) => (
                <SortableItem key={item}>
                  <div style={{ display: 'flex' }}>
                    <SortableKnob>
                      <span
                        aria-label={__('Move item', 'wp-newsletter-builder')}
                        style={{ width: '15px', height: '100%', cursor: 'move' }}
                      >
                        ::
                      </span>
                    </SortableKnob>
                    {item === 'title' ? (
                      <p>{__('Title', 'wp-newsletter-builder')}</p>
                    ) : (
                      <CheckboxControl
                        label={sprintf(__('Show %s', 'wp-newsletter-builder'), item)}
                        checked={isShown(item)}
                        onChange={() => toggleShown(item)}
                      />
                    )}
                  </div>
                </SortableItem>
              ))}
            </SortableList>
          </PanelRow>
        </PanelBody>
        {postId ? (
          <>
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
            <PanelBody
              title={__('Override URL', 'wp-newsletter-builder')}
              initialOpen
            >
              <PanelRow>
                <TextControl
                  value={overrideUrl ?? ''}
                  onChange={(newValue: string) => setAttributes({ overrideUrl: newValue })}
                  type="url"
                />
              </PanelRow>
            </PanelBody>
          </>
        ) : null}
      </InspectorControls>
    </div>
  );
}
