import { useEffect, useMemo, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import useNewsletterMeta from '@/hooks/useNewsletterMeta';
import sendsEmail from '@/services/sends-email';

interface ListResult {
  ListID: string;
  Name: string;
}

export interface Option {
  value: string;
  label: string;
}

function useEmailLists() {
  const { meta } = useNewsletterMeta();
  const [lists, setLists] = useState<ListResult[]>([]);
  const [listsLoaded, setListsLoaded] = useState(false);

  const emailListOptions = useMemo(() => {
    if (lists.length === 0) {
      return [];
    }

    return lists
      .map((item: ListResult) => ({ label: item.Name, value: item.ListID }));
  }, [lists]);
  const selectedEmailList = useMemo(() => emailListOptions
    .filter((item: Option) => meta.list.includes(item.value)), [meta.list, emailListOptions]);

  // Fetch once on mount. Depending on `lists` here would refetch forever when the
  // endpoint returns an empty array.
  useEffect(() => {
    if (!sendsEmail()) {
      return;
    }
    apiFetch({ path: '/wp-newsletter-builder/v1/lists' })
      .then((response) => {
        setLists(Array.isArray(response) ? response as ListResult[] : []);
      })
      .catch(() => setLists([]))
      .finally(() => setListsLoaded(true));
  }, []);

  return {
    emailListOptions,
    listsLoaded,
    selectedEmailList,
  };
}

export default useEmailLists;
