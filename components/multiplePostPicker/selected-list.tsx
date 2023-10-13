import SortableList, { SortableItem, SortableKnob } from 'react-easy-sort';

import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { arrayMoveImmutable } from 'array-move';
import type { WP_REST_API_Search_Result } from 'wp-types';

import './selected-list.scss';

interface SelectedListProps {
  searchRender: (post: object) => JSX.Element;
  selected?: WP_REST_API_Search_Result[];
  setSelected: (posts: WP_REST_API_Search_Result[]) => void;
}

/**
 * Displays a list of posts in the post picker modal.
 *
 * @param {obj} atts The attributes of the SelectedList.
 */
function SelectedList({
  searchRender,
  selected,
  setSelected,
}: SelectedListProps) {
  const removeFromSelected = (removeValue: WP_REST_API_Search_Result) => {
    setSelected(selected ? selected.filter((item) => item.id !== removeValue.id) : []);
  };

  const onSortEnd = (oldIndex: number, newIndex: number) => {
    const newSelected = arrayMoveImmutable(
      [...selected as WP_REST_API_Search_Result[]],
      oldIndex,
      newIndex,
    );
    setSelected(newSelected);
  };

  return (
    <SortableList
      onSortEnd={onSortEnd}
      className="nb-sortable-list alley-scripts-post-picker__post-list"
      lockAxis="y"
    >
      {selected ? (
        selected.map((t) => (
          <SortableItem key={t.id}>
            <div className="nb-post-picker-draggable">
              <SortableKnob>
                <span aria-label={__('Move item', 'newsletter-builder')}>::</span>
              </SortableKnob>
              { /* @ts-ignore */ }
              <Button
                className="nb-post-picker__post"
                onClick={() => removeFromSelected(t)}
              >
                {searchRender(t)}
              </Button>
            </div>
          </SortableItem>
        ))
      ) : null}
    </SortableList>
  );
}

export default SelectedList;
