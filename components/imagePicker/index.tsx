/* eslint-disable camelcase */
import { __ } from '@wordpress/i18n';
import { BaseControl, Button, ButtonGroup } from '@wordpress/components';
import { useEffect, useRef, useState } from 'react';
import apiFetch from '@wordpress/api-fetch';
import type { WP_REST_API_Attachment } from 'wp-types';
import './index.scss';

declare global {
  interface Window {
    wp: {
      media: (options: MediaLibraryOptions) => any;
    };
  }
}

type ImagePickerProps = {
  label: string;
  onChange: (value: number) => void;
  value: number;
};

type MediaLibraryOptions = {
  library: {
    type: string;
  };
};

type MediaLibrarySelection = {
  id: number;
  url: string;
};

export default function ImagePicker({
  label,
  onChange,
  value,
}: ImagePickerProps) {
  const imagePreviewRef = useRef<HTMLImageElement>(null);
  const imagePreview = imagePreviewRef.current as HTMLImageElement;
  const [imageUrl, setImageUrl] = useState('');

  /**
   * Handle the Media Library modal logic.
   * @returns Promise
   */
  const openMediaLibraryModal = () => {
    return new Promise((resolve, reject) => {      
      // Create the Media Library object. Restrict to images only.
      const mediaLibrary = window?.wp?.media({
        library: {
          type: 'image'
        }
      });
  
      if (!mediaLibrary) {
        reject();
      }
  
      // Set up the select event listener. On success, returns a promise with the image ID and URL.
      mediaLibrary?.on('select', () => {
        const selectedImage = mediaLibrary?.state()?.get('selection')?.first();

        if (!selectedImage) {
          reject();
        }
  
        const {
          attributes: {
            id = 0,
            url = '',
          } = {},
        } = selectedImage;

        resolve({ id, url });
      });
  
      // Open the Media Library modal.
      mediaLibrary?.open();
    });
  };

  /**
   * Select an image.
   */
  const selectImage = async () => {
    const imageData = await openMediaLibraryModal() as MediaLibrarySelection;

    const {
      id = 0,
      url = '',
    } = imageData;

    // Pass the selected attachment ID to the onChange event.
    onChange(id);

    // Update the image URL state.
    setImageUrl(url);
  }

  /**
   * Clear the selected image.
   */
  const clearImage = () => {
    onChange(0);
    setImageUrl('');
  };

  /**
   * Fetch the image URL from the REST API when the image ID changes.
   */
  useEffect(() => {
    if (!value) {
      return;
    }

    // Get the image url from the REST API and update the image preview.
    apiFetch({ path: `/wp/v2/media/${value}` })
      .then((response) => {
        const { source_url: url = '' } = response as WP_REST_API_Attachment;
        setImageUrl(url);
      })
      .catch(() => {
        setImageUrl('');
      });
  }, [value]);

  /**
   * Update the image preview when the image URL changes.
   */
  useEffect(() => {
    if (!imageUrl || !imagePreview) {
      return;
    }

    imagePreview.src = imageUrl;
  }, [imageUrl]);

  return (
    <BaseControl label={label}>
      <ButtonGroup className="image-picker__button-group">
        <Button
          onClick={selectImage}
          variant="secondary"
        >
          {__('Select an Image', 'wp-newsletter-builder')}
        </Button>
        <Button
          disabled={!imageUrl}
          onClick={clearImage}
          variant="secondary"
        >
          {__('Clear Image', 'wp-newsletter-builder')}
        </Button>
      </ButtonGroup>
      <img
        className="image-picker__preview"
        ref={imagePreviewRef}
        src={imageUrl}
      />
    </BaseControl>
  );
}
