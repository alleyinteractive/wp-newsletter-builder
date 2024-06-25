import { useCallback, useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

interface Status {
  Bounced?: number;
  Clicks?: number;
  Forwards?: number;
  Likes?: number;
  Mentions?: number;
  Name?: string;
  Recipients?: number;
  SpamComplaints?: number;
  Status?: string;
  TotalOpened?: number;
  UniqueOpened?: number;
  Unsubscribed?: number;
  WebVersionTextURL?: string;
  WebVersionURL?: string;
  WorldviewURL?: string;
}

function useNewsletterStatus(newsletterId: string) {
  const [status, setStatus] = useState<Status>({});
  const [fetching, setFetching] = useState(false);

  const fetchStatus = useCallback(async () => {
    setFetching(true);
    const res = await apiFetch({
      path: `/wp-newsletter-builder/v1/status/${newsletterId}`,
    });
    setStatus(res as Status);
    setFetching(false);
  }, [newsletterId]);

  useEffect(() => {
    fetchStatus();
  }, [fetchStatus]);

  return {
    validStatus: status.Status && status.Name,
    status,
    fetching,
    fetchStatus,
  };
}

export default useNewsletterStatus;
