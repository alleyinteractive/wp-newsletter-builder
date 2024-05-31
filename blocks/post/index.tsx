/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */
import edit from './edit';
import metadata from './block.json';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(
  /* @ts-expect-error Provided types are inaccurate to the actual plugin API. */
  metadata,
  {
    edit,
    save: () => {
      const blockProps = useBlockProps.save();
      return (
        // Role='presentation' tells AT table is for layout only so table semantics are ignored.
        <table {...blockProps} role="presentation">
          <tbody>
            <tr>
              {/* @ts-ignore */}
              <InnerBlocks.Content />
            </tr>
          </tbody>
        </table>
      );
    },
  },
);
