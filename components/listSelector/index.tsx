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
    output.unshift({ label: __('Select a list', 'newsletter-builder'), value: '' });
    return output;
  };

  const options = lists.length > 0 ? listsToOptions(lists) : [];

  useEffect(() => {
    if (lists.length > 0) {
      return;
    }
    apiFetch({ path: '/newsletter-builder/v1/lists' }).then((response) => {
      setLists(response as any as ListResult[]);
    });
  }, [lists]);

  return (
    <SelectControl
      value={selected}
      options={options}
      onChange={updateFunction}
    />
  );
}
