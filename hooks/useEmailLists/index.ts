import { useEffect, useMemo, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import useNewsletterMeta from '@/hooks/useNewsletterMeta';

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

  const emailListOptions = useMemo(() => {
    if (lists.length === 0) {
      return [];
    }

    return lists
      .map((item: ListResult) => ({ label: item.Name, value: item.ListID }));
  }, [lists]);
  const selectedEmailList = useMemo(() => emailListOptions
    .filter((item: Option) => meta.list.includes(item.value)), [meta.list, emailListOptions]);

  useEffect(() => { // eslint-disable-line
    if (lists.length > 0) {
      return;
    }
    apiFetch({ path: '/wp-newsletter-builder/v1/lists' }).then((response) => {
      setLists(response as any as ListResult[]);
    });
  }, [lists]);

  return {
    emailListOptions,
    selectedEmailList,
  };
}

export default useEmailLists;
