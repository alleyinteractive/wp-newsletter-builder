/* eslint-disable camelcase */
import { usePostById } from '@alleyinteractive/block-editor-tools';
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { WP_REST_API_Post } from 'wp-types';

interface EditProps {
  attributes: {
    overrideTitle?: string;
    smallerFont?: boolean;
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
    overrideTitle,
    smallerFont,
  },
  context: {
    postId,
  },
  setAttributes,
}: EditProps) {
  // @ts-ignore
  const record: WP_REST_API_Post = usePostById(postId) ?? null;

  let postTitle = record ? record.title.rendered : __('Post Title', 'wp-newsletter-builder');

  postTitle = overrideTitle || postTitle;

  const titleClass = smallerFont ? 'post__title--small' : '';

  return (
    <h2 {...useBlockProps({ className: titleClass })}>
      <RichText
        value={postTitle}
        tagName="span"
        onChange={(value) => setAttributes({ overrideTitle: value })}
      />
    </h2>
  );
}
