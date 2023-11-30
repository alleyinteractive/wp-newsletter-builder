/* eslint-disable camelcase */
import { usePostById } from '@alleyinteractive/block-editor-tools';
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { WP_REST_API_Post } from 'wp-types';

import './index.scss';

interface EditProps {
  attributes: {
    overrideContent?: string;
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
    overrideContent,
  },
  context: {
    postId,
  },
  setAttributes,
}: EditProps) {
  // @ts-ignore
  const record: WP_REST_API_Post = usePostById(postId) ?? null;

  let postContent = record ? record.content.rendered : __('<p>This block will display the content.</p>', 'wp-newsletter-builder');

  const removeLinks = (html: string) => (
    html ? html.replace(/<a[^>]*?>(.*?)<\/a>/gi, '$1') : ''
  );

  const paragraphs = postContent.match(/<p(.*?)<\/p>/gi) || [];
  // TODO: Add attribute and slider for number of paragraphs to display.
  postContent = overrideContent || removeLinks(paragraphs?.slice(0, 2).join(''));

  return (
    <div {...useBlockProps({ className: 'post__content' })}>
      <RichText
        value={postContent}
        tagName="div"
        multiline
        onChange={(value) => setAttributes({ overrideContent: value })}
      />
    </div>
  );
}
