import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

type ListSelectorProps = {
  selected: string;
  updateFunction: (value: string) => void;
};

interface ListResult {
  ListID: string;
  Name: string;
}

export default function ListSelector({
  selected,
  updateFunction,
}: ListSelectorProps) {
  const [lists, setLists] = useState<ListResult[]>([]);

  const listsToOptions = (rawLists: ListResult[]) => {
    const output = rawLists.map((item: ListResult) => ({ label: item.Name, value: item.ListID }));
    output.unshift({ label: __('Select a list', 'wp-newsletter-builder'), value: '' });
    return output;
  };

  const options = lists.length > 0 ? listsToOptions(lists) : [];

  // Fetch once on mount. Depending on `lists` here would refetch forever when the
  // endpoint returns an empty array.
  useEffect(() => {
    apiFetch({ path: '/wp-newsletter-builder/v1/lists' }).then((response) => {
      setLists(Array.isArray(response) ? response as ListResult[] : []);
    });
  }, []);

  return (
    <SelectControl
      value={selected}
      options={options}
      onChange={updateFunction}
    />
  );
}
