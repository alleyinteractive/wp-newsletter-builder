import {
  InnerBlocks,
  useBlockProps,
  InspectorControls,
  RichText,
} from '@wordpress/block-editor';
import { dispatch, select } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { PanelBody, PanelRow } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';

import MultiplePostPicker from '@/components/multiplePostPicker';
import PostPickerResult from '@/components/postPickerResult';
import './index.scss';

interface EditProps {
  clientId: string;
  attributes: {
    showNumbers?: boolean;
    heading?: string;
  },
  setAttributes: (attributes: {}) => void;
}

export interface Block {
  clientId: string;
  name: string;
  innerBlocks?: Block[];
  attributes: {
    postId?: number;
    number?: number;
  }
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
  clientId,
  attributes: {
    showNumbers = false,
    heading = '',
  },
  setAttributes,
}: EditProps) {
  /**
   * Recursively gets all client ids from the array of inner blocks.
   *
   * @param {array} innerBlocks The array of blocks.
   * @param {array} blockNames The array of blockNames to match. Other blocks will be ignored.
   * @param {array} ids The array of client ids.
   * @returns {array}
   */
  function getClientIdsFromInnerBlocks(
    innerBlocks: Block[],
    blockNames: string[],
    ids: string[],
  ): string[] {
    innerBlocks.forEach((block: Block) => {
      if (blockNames.includes(block.name)) {
        ids.push(block.clientId);
      }
      if (block.innerBlocks && block.innerBlocks.length > 0) {
        return getClientIdsFromInnerBlocks(block.innerBlocks, blockNames, ids);
      }
      return ids;
    });
    return ids;
  }

  const cutoff = new Date();
  cutoff.setMonth(cutoff.getMonth() - 3);

  const block = select('core/block-editor').getBlocksByClientId(clientId)[0] || null;
  const blocks = block ? block.innerBlocks : [];
  const postBlocks = getClientIdsFromInnerBlocks(blocks, ['wp-newsletter-builder/post'], []);

  const handleSelect = (posts: number[]) => {
    postBlocks.forEach((id: string) => {
      const postId = posts.shift();
      dispatch('core/block-editor').updateBlockAttributes(id, { postId });
    });
  };

  useEffect(() => {
    if (showNumbers) {
      postBlocks.forEach((id: string, index: number) => {
        dispatch('core/block-editor').updateBlockAttributes(id, { number: index + 1 });
      });
    }
  }, [postBlocks, showNumbers]);

  return (
    <>
      <div {...useBlockProps()}>
        {heading ? (
          <RichText
            tagName="h2"
            value={heading}
            onChange={(value) => setAttributes({ heading: value })}
            className="wp-newsletter-builder-section__heading"
          />
        ) : null}
        <InnerBlocks />
      </div>
      <InspectorControls>
        { /* @ts-ignore */}
        <PanelBody
          title={__('Post Selection', 'wp-newsletter-builder')}
          initialOpen
        >
          { /* @ts-ignore */}
          <PanelRow>
            <MultiplePostPicker
              onUpdate={handleSelect}
              allowedTypes={applyFilters('wpNewsletterBuilder.allowedPostTypes', ['post']) as string[]} // Allow filtering of allowed post types. Defaults to post.
              params={{ after: cutoff.toISOString(), per_page: 20 }}
              // @ts-ignore
              searchRender={PostPickerResult}
            />
          </PanelRow>
        </PanelBody>
      </InspectorControls>
    </>
  );
}
