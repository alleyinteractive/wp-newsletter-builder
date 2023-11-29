/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { usePostById, useMedia, ImagePicker } from '@alleyinteractive/block-editor-tools';

import {
  PanelBody,
  PanelRow,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './index.scss';

interface EditProps {
  attributes: {
    overrideImage?: number;
  };
  context: {
    postId: number;
  };
  setAttributes: (attributes: {}) => void;
}

interface Post {
  title: {
    rendered: string;
  };
  featured_media: number;
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
    overrideImage,
  },
  context: {
    postId,
  },
  setAttributes,
}: EditProps) {
  // @ts-ignore
  const record: Post = usePostById(postId) ?? null;
  let featuredMediaId = record ? record.featured_media : null;
  const postTitle = record ? record.title.rendered : '';

  featuredMediaId = overrideImage || featuredMediaId;

  const media = useMedia(featuredMediaId) ?? null;

  const postImage = media ? media.source_url : '';

  return (
    <>
      <div {...useBlockProps({ className: 'image-container' })}>
        <img src={postImage} alt={postTitle} />
      </div>
      <InspectorControls>
        {postId ? (
          <PanelBody
            title={__('Override Image', 'wp-newsletter-builder')}
            initialOpen
          >
            {/* @ts-ignore */}
            <PanelRow>
              <ImagePicker
                value={overrideImage ?? 0}
                onUpdate={({ id }) => setAttributes({ overrideImage: id })}
                onReset={() => setAttributes({ overrideImage: 0 })}
              />
            </PanelRow>
          </PanelBody>
        ) : null}
      </InspectorControls>
    </>
  );
}
