/* eslint-disable camelcase */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';

interface EditProps {
  attributes: {
    readMoreText?: string;
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
    readMoreText,
  },
  setAttributes,
}: EditProps) {
  const buttonText = readMoreText || __('Read More', 'wp-newsletter-builder');

  return (
    <div {...useBlockProps({ className: 'newsletter-read-more has-text-align-center' })}>
      <RichText
        tagName="span"
        className="wp-element-button"
        value={buttonText}
        onChange={(value) => setAttributes({ readMoreText: value })}
      />
    </div>
  );
}
