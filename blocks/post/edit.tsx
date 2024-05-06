/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { PostPicker } from '@alleyinteractive/block-editor-tools';
import { useSelect } from '@wordpress/data';
import {
  Button,
  PanelBody,
  PanelRow,
  TextControl,
} from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls, InnerBlocks } from '@wordpress/block-editor';

import PostPickerResult from '@/components/postPickerResult';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './index.scss';

interface EditProps {
  attributes: {
    postId: number;
    overrideUrl: string;
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
    postId = 0,
    overrideUrl = '',
  },
  setAttributes,
}: EditProps) {
  const handleSelect = (selected: number) => {
    setAttributes({
      postId: selected,
    });
  };

  const editPostType = useSelect(
    // @ts-ignore
    (select) => select('core/editor').getCurrentPostType(),
    [],
  );

  const cutoff = new Date();
  cutoff.setMonth(cutoff.getMonth() - 3);

  // TODO: Pass template and allowed blocks from PHP so they can be filtered.
  const MY_TEMPLATE = [
    ['wp-newsletter-builder/post-featured-image', {}],
    ['wp-newsletter-builder/post-title', {}],
    ['wp-newsletter-builder/post-byline', {}],
    ['wp-newsletter-builder/post-excerpt', {}],
    ['wp-newsletter-builder/post-read-more', {}],
  ];
  const ALLOWED_BLOCKS = [
    'wp-newsletter-builder/post-byline',
    'wp-newsletter-builder/post-content',
    'wp-newsletter-builder/post-excerpt',
    'wp-newsletter-builder/post-featured-image',
    'wp-newsletter-builder/post-read-more',
    'wp-newsletter-builder/post-title',
  ];

  return (
    <div {...useBlockProps({ className: `edit-${editPostType}` })}>
      {postId && editPostType !== 'nb_template' ? (
        <>
          {/* @ts-ignore */}
          <Button
            variant="primary"
            isDestructive
            onClick={() => handleSelect(0)}
            className="newsletter-remove-post"
            aria-label={__('Remove Post', 'wp-newsletter-builder')}
          >
            X
          </Button>
        </>
      ) : null}
      { !postId && editPostType !== 'nb_template' ? (
        <div>
          {/* @ts-ignore */}
          <PostPicker
            onUpdate={handleSelect}
            allowedTypes={applyFilters('wpNewsletterBuilder.allowedPostTypes') as string[] ?? ['post']} // Allow filtering of allowed post types.
            onReset={() => handleSelect(0)}
            params={{ after: cutoff.toISOString(), per_page: 20 }}
            title={__('Please select a post', 'wp-newsletter-builder')}
            value={postId}
            // @ts-ignore
            searchRender={PostPickerResult}
          />
        </div>
      ) : null}
      { postId || editPostType === 'nb_template' ? (
        <InnerBlocks
          // @ts-ignore
          template={MY_TEMPLATE}
          allowedBlocks={ALLOWED_BLOCKS}
          templateLock={false}
        />
      ) : null}
      <InspectorControls>
        {/* @ts-ignore */}
        {postId ? (
          <PanelBody
            title={__('Override URL', 'wp-newsletter-builder')}
            initialOpen
          >
            <PanelRow>
              <TextControl
                value={overrideUrl ?? ''}
                onChange={(newValue: string) => setAttributes({ overrideUrl: newValue })}
                type="url"
              />
            </PanelRow>
          </PanelBody>
        ) : null}
      </InspectorControls>
    </div>
  );
}
