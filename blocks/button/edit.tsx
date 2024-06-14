import { useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, InnerBlocks } from '@wordpress/block-editor';
import { ColorPicker, PanelBody } from '@wordpress/components';
import useInnerBlockAttributes from '@/hooks/use-inner-blocks-attributes';

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
    align?: 'center' | 'left' | 'right';
    bgColor?: string;
    btnWidth?: string,
    radius?: string,
    textColor?: string,
  };
  setAttributes: (attributes: {}) => void;
  clientId: string,
}

export default function Edit({
  attributes: {
    bgColor = '#0279af',
    btnWidth = 'max-content',
    radius = '0',
    textColor = '#fff',
  },
  setAttributes,
  clientId,
}: EditProps) {
  const TEMPLATE = [['core/button']];
  const innerBlockAttributes = useInnerBlockAttributes(clientId);
  const innerBorderRadius = innerBlockAttributes[0]?.style?.border?.radius || '0';
  const innerWidth = innerBlockAttributes[0]?.width;
  const buttonStyles = {
    backgroundColor: bgColor,
    color: textColor,
    borderRadius: radius,
    width: btnWidth,
    margin: '0 auto',
  };

  useEffect(() => {
    setAttributes({
      radius: innerBorderRadius,
      btnWidth: innerWidth !== undefined ? `${innerWidth}%` : 'max-content',
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [innerBorderRadius, innerWidth]);

  return (
    <>
      <InspectorControls>
        <PanelBody title="Button Color">
          <h3>{__('Background color', 'wp-newsletter-builder')}</h3>
          {/* Using ColorPicker instead of ColorPalette to ensure email-friendly values. */}
          <ColorPicker
            color={bgColor}
            onChange={(color) => setAttributes({ bgColor: color })}
          />
          <h3>{__('Text color', 'wp-newsletter-builder')}</h3>
          <ColorPicker
            color={textColor}
            onChange={(color) => setAttributes({ textColor: color })}
          />
        </PanelBody>
      </InspectorControls>
      <div {...useBlockProps({ style: buttonStyles })}>
        <InnerBlocks
          // @ts-ignore
          template={TEMPLATE}
          templateLock="all"
        />
      </div>
    </>
  );
}
