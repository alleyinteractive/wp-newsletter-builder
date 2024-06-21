/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { TermSelector } from '@alleyinteractive/block-editor-tools';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
  PanelBody,
  PanelRow,
  RangeControl,
} from '@wordpress/components';

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

export default function Edit() {
  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Post List Settings', 'wp-newsletter-builder')}>
          <PanelRow>
            <RangeControl
              label={__('Number of posts', 'wp-newsletter-builder')}
              // value={postCount}
              // onChange={(newValue: number) => {
              //   providePostListAttributes({ postCount: newValue });
              // }}
              min={1}
              max={10}
            />
          </PanelRow>
          <PanelRow>
            <h3>{__('Term Picker', 'wp-newsletter-builder')}</h3>
            <TermSelector
              className=""
              emptyLabel={__('No term found', 'lede')}
              label={__('Search for a term', 'lede')}
              maxPages={5}
              maxSelections={1}
              multiple={false}
              placeholder={__('Search for a term', 'wp-newsletter-builder')}
              subTypes={[]}
              // selected={(termId && termTitle) ? [{ id: termId, title: termTitle }] : []}
              threshold={3}
              // onSelect={(selectedTerms: any) => {
              //   const selectedTerm = selectedTerms[0];

              //   providePostListAttributes({
              //     termId: selectedTerm.id.toString(), // WPgraphQL expects termId to be a string
              //     taxonomy: selectedTerm.type,
              //     termTitle: selectedTerm.title,
              //     title: selectedTerm.title,
              //   });
              // }}
            />
          </PanelRow>
        </PanelBody>
      </InspectorControls>
      <p {...useBlockProps()}>
        { __('Block Title - hello from the editor!', 'wp-newsletter-builder') }
      </p>
    </>
  );
}
