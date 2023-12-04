/* eslint-disable camelcase */
import { usePostById } from '@alleyinteractive/block-editor-tools';
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { WP_REST_API_Post } from 'wp-types';

interface EditProps {
  attributes: {
    overrideExcerpt?: string;
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
    overrideExcerpt,
  },
  context: {
    postId,
  },
  setAttributes,
}: EditProps) {
  // @ts-ignore
  const record: WP_REST_API_Post = usePostById(postId) ?? null;

  let postExcerpt = record ? record.excerpt.rendered : __('This block will display the excerpt.', 'wp-newsletter-builder');

  postExcerpt = overrideExcerpt || postExcerpt;

  return (
    <div {...useBlockProps({ className: 'post__dek' })}>
      <RichText
        value={postExcerpt}
        tagName="p"
        multiline={false}
        onChange={(value) => setAttributes({ overrideExcerpt: value })}
      />
    </div>
  );
}
