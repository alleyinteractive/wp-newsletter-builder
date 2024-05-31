/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

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
const MY_TEMPLATE = [
  ['wp-newsletter-builder/post-item', {}],
  ['wp-newsletter-builder/post-item', {}],
];

export default function Edit() {
  return (
    <table {...useBlockProps()} role="presentation">
      <tbody>
        <tr>
          <InnerBlocks
            orientation="horizontal"
            // @ts-ignore
            template={MY_TEMPLATE}
            templateLock="all"
          />
        </tr>
      </tbody>
    </table>
  );
}
