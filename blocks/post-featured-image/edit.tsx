/* eslint-disable camelcase */
import { usePostById, useMedia, ImagePicker } from '@alleyinteractive/block-editor-tools';
import { PanelBody, PanelRow, Placeholder } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { WP_REST_API_Post, WP_REST_API_Attachment } from 'wp-types';

import './index.scss';

interface EditProps {
  attributes: {
    overrideImage?: number;
  };
  context: {
    postId: number;
  };
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
    overrideImage,
  },
  context: {
    postId,
  },
  setAttributes,
}: EditProps) {
  // @ts-ignore
  const record: WP_REST_API_Post = usePostById(postId) ?? null;
  let featuredMediaId = record ? record.featured_media : null;
  const postTitle = record ? record.title.rendered : '';

  featuredMediaId = overrideImage || featuredMediaId;

  const media = useMedia(featuredMediaId) as any as WP_REST_API_Attachment;

  const postImage = media ? media.source_url : '';

  return (
    <>
      <div {...useBlockProps({ className: 'image-container' })}>
        {record && postImage !== '' ? (
          <img src={postImage} alt={postTitle} />
        ) : null}
        { !record ? (
          <Placeholder
            className="block-editor-media-placeholder"
            style={{ aspectRatio: '16 / 9' }}
          />
        ) : null}
      </div>
      <InspectorControls>
        {postId ? (
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
        ) : null}
      </InspectorControls>
    </>
  );
}
