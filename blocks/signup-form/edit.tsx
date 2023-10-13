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
import {
  InspectorControls,
  InnerBlocks,
  RichText,
  useBlockProps,
} from '@wordpress/block-editor';

import {
  PanelBody,
  PanelRow,
} from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './index.scss';

import ListSelector from '../../components/listSelector';

type EditProps = {
  attributes: {
    headerText: string;
    subheaderText?: string;
    disclaimerText?: string;
    buttonText: string;
    listId?: string;
  };
  setAttributes: (value: any) => void;
};

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
    headerText,
    subheaderText = '',
    disclaimerText = '',
    buttonText,
    listId,
  },
  setAttributes,
}: EditProps) {
  return (
    <>
      <div {...useBlockProps()}>
        <div className="wp-block-newsletter-builder-signup-form__header">
          <div>
            <RichText
              tagName="h2"
              value={headerText}
              onChange={(value) => setAttributes({ headerText: value })}
              placeholder={__('Header Text', 'newsletter-builder')}
            />
            <RichText
              tagName="div"
              value={subheaderText}
              className="wp-block-newsletter-builder-signup-form__subheader"
              onChange={(value) => setAttributes({ subheaderText: value })}
              placeholder={__('Subheader Text', 'newsletter-builder')}
            />
          </div>
          <div>
            <div>
              <span className="wp-block-newsletter-builder-signup-form__fake-text-input wp-block-newsletter-builder-signup-form__email-input">
                {__('Email', 'newsletter-builder')}
              </span>
            </div>
            <RichText
              tagName="div"
              value={disclaimerText}
              className="wp-block-newsletter-builder-signup-form__disclaimer"
              onChange={(value) => setAttributes({ disclaimerText: value })}
              placeholder={__('Disclaimer Text', 'newsletter-builder')}
            />

            <div className="wp-block-button is-style-subscribe">
              <div className="wp-block-newsletter-builder-signup-form__fake-button wp-block-button__link wp-element-button">
                <RichText
                  tagName="span"
                  value={buttonText}
                  onChange={(value) => setAttributes({ buttonText: value })}
                  placeholder={__('Button Text', 'newsletter-builder')}
                />
              </div>
            </div>
          </div>
        </div>
        {!listId ? (
          <InnerBlocks
            allowedBlocks={['newsletter-builder/signup-form-list']}
          />
        ) : null }
      </div>
      <InspectorControls>
        <PanelBody title={__('List Settings', 'newsletter-builder')}>
          <PanelRow>
            <ListSelector
              selected={listId ?? ''}
              updateFunction={(value) => setAttributes({ listId: value })}
            />
          </PanelRow>
        </PanelBody>
      </InspectorControls>
    </>
  );
}
