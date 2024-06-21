/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
// import { PostPicker } from '@alleyinteractive/block-editor-tools';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './index.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
// Allow filtering of allowed post types. Defaults to post.
// interface Window {
//   newsletterBuilder: {
//     allowedPostTypes: Array<string>;
//   };
// }

// const {
//   newsletterBuilder: {
//     allowedPostTypes = ['post'],
//   } = {},
// } = (window as any as Window);

export default function Edit() {
  return (
    <p {...useBlockProps()}>
      { __('Block Title - hello from the editor!', 'wp-newsletter-builder') }
    </p>
  );
}
