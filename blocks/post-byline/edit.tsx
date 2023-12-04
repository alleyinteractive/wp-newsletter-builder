/* eslint-disable camelcase */
import { usePostById } from '@alleyinteractive/block-editor-tools';
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { WP_REST_API_Post } from 'wp-types';

interface EditProps {
  attributes: {
    overrideByline?: string;
  };
  context: {
    postId: number;
  };
  setAttributes: (attributes: {}) => void;
}

interface PostWithByline extends WP_REST_API_Post {
  wp_newsletter_builder_byline: string;
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
    overrideByline,
  },
  context: {
    postId,
  },
  setAttributes,
}: EditProps) {
  // @ts-ignore
  const record: PostWithByline = usePostById(postId) ?? null;

  let postByline = record ? record.wp_newsletter_builder_byline : __('Post Byline', 'wp-newsletter-builder');

  postByline = overrideByline || postByline;

  return (
    <p {...useBlockProps({ className: 'post__byline' })}>
      <RichText
        value={postByline}
        tagName="span"
        onChange={(value) => setAttributes({ overrideByline: value })}
        allowedFormats={[]}
      />
    </p>
  );
}
