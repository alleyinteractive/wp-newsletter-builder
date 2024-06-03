/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import {
  PanelBody,
  RangeControl,
} from '@wordpress/components';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
// Uncomment the following line if styles are added.
// import './index.scss';

interface EditProps {
  attributes: {
    elHeight: number;
  },
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
    elHeight = 8,
  },
  setAttributes,
}: EditProps) {
  return (
    <>
      <div {...useBlockProps()}>
        <div style={{ backgroundColor: '#000', height: `${elHeight}px` }} />
      </div>
      <InspectorControls>
        {/* @ts-ignore */}
        <PanelBody
          title={__('Settings', 'wp-newsletter-builder')}
          initialOpen
        >
          <RangeControl
            label={__('Height (in pixels)', 'wp-newsletter-builder')}
            help={__('Minimum value: 1px. Maximum value: 24px.', 'wp-newsletter-builder')}
            value={elHeight}
            onChange={(newValue) => setAttributes({ elHeight: newValue })}
            min={1}
            max={24}
            resetFallbackValue={8}
            allowReset
          />
        </PanelBody>
      </InspectorControls>
    </>
  );
}
