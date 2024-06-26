import { useCallback, useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

interface Stats {
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

function useNewsletterStats(newsletterId: string) {
  const [stats, setStats] = useState<Stats>({});
  const [fetching, setFetching] = useState(false);

  const fetchStats = useCallback(async () => {
    setFetching(true);
    const res = await apiFetch({
      path: `/wp-newsletter-builder/v1/status/${newsletterId}`,
    });
    setStats(res as Stats);
    setFetching(false);
  }, [newsletterId]);

  useEffect(() => {
    fetchStats();
  }, [fetchStats]);

  return {
    validStats: stats.Status && stats.Name,
    stats,
    fetching,
    fetchStats,
  };
}

export default useNewsletterStats;
