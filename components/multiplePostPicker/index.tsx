import { useState } from '@wordpress/element';

import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';

import SearchModal from './search-modal';

interface MultiplePostPickerProps {
  allowedTypes?: string[];
  onUpdate: (ids: number[]) => void;
  params?: object;
  searchEndpoint?: string;
  searchRender: (post: object) => JSX.Element;
}

function MultiplePostPicker({
  allowedTypes,
  onUpdate,
  params = {},
  searchEndpoint = '/wp/v2/search',
  searchRender,
}: MultiplePostPickerProps) {
  const [showModal, setShowModal] = useState(false);

  const baseUrl = addQueryArgs(
    searchEndpoint,
    {
      type: 'post',
      subtype: allowedTypes ?? 'any',
      ...params,
    },
  );

  const openModal = () => {
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
  };

  return (
    <>
      { /* @ts-ignore */}
      <Button
        onClick={openModal}
        variant="secondary"
      >
        {__('Select Posts', 'alley-scripts')}
      </Button>
      {showModal ? (
        <SearchModal
          closeModal={closeModal}
          baseUrl={baseUrl}
          onUpdate={onUpdate}
          searchRender={searchRender}
        />
      ) : null}
    </>
  );
}

export default MultiplePostPicker;
