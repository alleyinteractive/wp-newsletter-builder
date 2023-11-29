/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { usePostById } from '@alleyinteractive/block-editor-tools';
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { RichText } from '@wordpress/block-editor';
import { WP_REST_API_Post } from 'wp-types';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './index.scss';

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
    <h2 className={titleClass}>
      <RichText
        value={postTitle}
        tagName="span"
        onChange={(value) => setAttributes({ overrideTitle: value })}
      />
    </h2>
  );
}
