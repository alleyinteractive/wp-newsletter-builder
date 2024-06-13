/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls, InnerBlocks } from '@wordpress/block-editor';
import { ColorPicker, PanelBody } from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
// Uncomment this line if you want to import a CSS file for this block.
// import './index.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
interface EditProps {
  attributes: {
    elColor?: string;
  };
  setAttributes: (attributes: {}) => void;
}

export default function Edit({
  attributes: {
    elColor = '#000',
  },
  setAttributes,
}: EditProps) {
  const TEMPLATE = [['core/heading']];
  const headingStyles = {
    color: elColor,
  };

  return (
    <>
      <InspectorControls>
        <PanelBody title="Heading Color">
          <h3>{__('Text color', 'wp-newsletter-builder')}</h3>
          {/* Using ColorPicker instead of ColorPalette to ensure email-friendly values. */}
          <ColorPicker
            color={elColor}
            onChange={(color) => setAttributes({ elColor: color })}
          />
        </PanelBody>
      </InspectorControls>
      <div {...useBlockProps({ className: 'wp-block-wp-newsletter-builder-heading', style: headingStyles })}>
        <InnerBlocks
          // @ts-ignore
          template={TEMPLATE}
          templateLock="all"
        />
      </div>
    </>
  );
}
