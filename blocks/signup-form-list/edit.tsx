/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import {
  Button,
  CheckboxControl,
  PanelBody,
  PanelRow,
} from '@wordpress/components';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {
  InspectorControls,
  MediaPlaceholder,
  RichText,
  useBlockProps,
} from '@wordpress/block-editor';

import ListSelector from '../../components/listSelector';

type EditProps = {
  attributes: {
    logo: number;
    title: string;
    frequency?: string;
    description?: string;
    listId: string;
    initialChecked: boolean;
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
    logo = 0,
    title = '',
    frequency = '',
    description = '',
    listId = '',
    initialChecked = false,
  },
  setAttributes,
}: EditProps) {
  const {
    logoMedia = null,
  } = useSelect((select) => ({
    // @ts-ignore
    logoMedia: logo ? select('core').getMedia(logo) : null,
  }), [logo]);

  return (
    <div {...useBlockProps()}>
      {logoMedia ? (
        <>
          <Button
            type="button"
            onClick={() => setAttributes({ logo: null })}
            aria-label={__('Remove Logo', 'newsletter-builder')}
            isDestructive
            variant="primary"
            className="wp-block-newsletter-builder-signup-form-list__image_delete"
          >
            X
          </Button>
          <img
            src={logoMedia.media_details?.sizes?.medium?.source_url || logoMedia.source_url}
            alt={__('Newsletter Logo', 'newsletter-builder')}
          />
        </>
      ) : (
        <MediaPlaceholder
          icon="format-image"
          labels={{
            title: __('Image', 'newsletter-builder'),
            instructions: __(
              'Drag an image, upload a new one or select a file from your library.',
              'newsletter-builder',
            ),
          }}
          onSelect={(value) => setAttributes({ logo: value.id })}
          accept="image/*"
          allowedTypes={['image']}
        />
      )}
      <div className="wp-block-newsletter-builder-signup-form-list__content">
        <RichText
          tagName="h3"
          value={title}
          onChange={(value) => setAttributes({ title: value })}
          placeholder={__('Title', 'newsletter-builder')}
        />
        <RichText
          tagName="div"
          value={frequency}
          className="wp-block-newsletter-builder-signup-form-list__frequency"
          onChange={(value) => setAttributes({ frequency: value })}
          placeholder={__('Frequency', 'newsletter-builder')}
        />
        <RichText
          tagName="div"
          value={description}
          className="wp-block-newsletter-builder-signup-form-list__description"
          onChange={(value) => setAttributes({ description: value })}
          placeholder={__('Description', 'newsletter-builder')}
        />
        <CheckboxControl
          checked={initialChecked}
          onChange={(value) => setAttributes({ initialChecked: value })}
        />
      </div>
      <InspectorControls>
        <PanelBody title={__('List Settings', 'newsletter-builder')}>
          <PanelRow>
            <ListSelector
              selected={listId}
              updateFunction={(value) => setAttributes({ listId: value })}
            />
          </PanelRow>
        </PanelBody>
      </InspectorControls>
    </div>
  );
}
