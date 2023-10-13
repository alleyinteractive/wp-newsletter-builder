import { useState } from '@wordpress/element';

import {
  Button,
  Modal,
} from '@wordpress/components';

import { __ } from '@wordpress/i18n';

import type { WP_REST_API_Search_Result } from 'wp-types';

import './search-modal.scss';
import PostList from './post-list';
import SelectedList from './selected-list';

interface SearchModalProps {
  baseUrl: string;
  closeModal: () => void;
  onUpdate: (ids: number[]) => void;
  searchRender: (post: object) => JSX.Element;
}

function SearchModal({
  baseUrl,
  closeModal,
  onUpdate,
  searchRender,
}: SearchModalProps) {
  const [selected, setSelected] = useState<WP_REST_API_Search_Result[]>([]);

  const doSelect = () => {
    if (!selected) {
      return;
    }
    const ids = selected.map((item: WP_REST_API_Search_Result) => item.id);
    onUpdate(ids as number[]);
    closeModal();
  };

  const addToSelected = (newValue: WP_REST_API_Search_Result) => {
    setSelected([...selected, newValue]);
  };

  return (
    // @ts-ignore
    <Modal
      isDismissible
      title={__('Select Post', 'alley-scripts')}
      onRequestClose={closeModal}
      closeButtonLabel="Close"
    >
      <div className="nb-multi-post-picker-modal">
        <div className="nb-multi-post-picker-modal__search-results">
          <PostList
            baseUrl={baseUrl}
            selected={selected ?? []}
            setSelected={addToSelected}
            searchRender={searchRender}
          />
        </div>
        <div className="nb-multi-post-picker-modal__selected">
          <SelectedList
            selected={selected}
            setSelected={setSelected}
            searchRender={searchRender}
          />
        </div>
      </div>
      <div className="alley-scripts-post-picker__buttons">
        { /* @ts-ignore */ }
        <Button
          variant="secondary"
          onClick={closeModal}
        >
          {__('Cancel', 'alley-scripts')}
        </Button>
        { /* @ts-ignore */ }
        <Button
          variant="primary"
          onClick={doSelect}
          disabled={!selected}
        >
          {__('Select', 'alley-scripts')}
        </Button>
      </div>
    </Modal>
  );
}

export default SearchModal;
