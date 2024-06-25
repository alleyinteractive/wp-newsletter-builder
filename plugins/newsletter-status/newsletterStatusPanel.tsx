import React, { useCallback, useEffect, useState } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { Button } from '@wordpress/components';
import NewsletterSpinner from '@/components/newsletterSpinner';

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

export default function NewsletterStatusPanel() {
  // @ts-ignore
  const postId = select('core/editor').getCurrentPostId();

  const [status, setStatus] = useState<Status>({});
  const [fetching, setFetching] = useState(false);

  const fetchStatus = useCallback(async () => {
    setFetching(true);
    const res = await apiFetch({
      path: `/wp-newsletter-builder/v1/status/${postId}`,
    });
    setStatus(res as Status);
    setFetching(false);
  }, [postId]);

  useEffect(() => {
    fetchStatus();
  }, [fetchStatus]);

  const {
    Status: statusString = '',
    Name = '',
    Recipients = '',
    TotalOpened = '',
    UniqueOpened = '',
  } = status;

  if (!statusString || !Name) {
    return (
      <PluginDocumentSettingPanel
        name="rubric-selection"
        title={__('Newsletter Status', 'wp-newsletter-builder')}
      >
        <p>
          {__('Newsletter status not available. Try clicking the Refresh button.', 'wp-newsletter-builder')}
        </p>
        <Button
          onClick={fetchStatus}
          variant="secondary"
          disabled={fetching}
        >
          {__('Refresh', 'wp-newsletter-builder')}
        </Button>
      </PluginDocumentSettingPanel>
    );
  }

  return (
    <PluginDocumentSettingPanel
      name="rubric-selection"
      title={__('Newsletter Status', 'wp-newsletter-builder')}
    >
      {status ? (
        <>
          <dl>
            <dt>
              {__('Status', 'wp-newsletter-builder')}
            </dt>
            <dd>
              {statusString}
            </dd>
            <dt>
              {__('Campaign Name', 'wp-newsletter-builder')}
            </dt>
            <dd>
              {Name}
            </dd>
            <dt>
              {__('Recipients', 'wp-newsletter-builder')}
            </dt>
            <dd>
              {Recipients}
            </dd>
            <dt>
              {__('Total Opened', 'wp-newsletter-builder')}
            </dt>
            <dd>
              {TotalOpened}
            </dd>
            <dt>
              {__('Unique Opened', 'wp-newsletter-builder')}
            </dt>
            <dd>
              {UniqueOpened}
            </dd>
          </dl>
          <Button
            onClick={fetchStatus}
            variant="secondary"
            disabled={fetching}
          >
            {__('Refresh', 'wp-newsletter-builder')}
          </Button>
        </>
      ) : (
        <NewsletterSpinner />
      )}
    </PluginDocumentSettingPanel>
  );
}
