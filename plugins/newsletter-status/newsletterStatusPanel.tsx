import React, { useCallback, useEffect, useState } from 'react';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { Button, Spinner } from '@wordpress/components';

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
      path: `/newsletter-builder/v1/status/${postId}`,
    });
    setStatus(res as Status);
    setFetching(false);
  }, [postId]);

  useEffect(() => {
    fetchStatus();
  }, [fetchStatus]);

  const {
    Status,
    Name = '',
    Recipients = null,
    TotalOpened = null,
    UniqueOpened = null,
  } = status;

  return (
    <PluginDocumentSettingPanel
      name="rubric-selection"
      title={__('Newsletter Status', 'newsletter-builder')}
    >
      {status ? (
        <>
          <dl>
            <dt>
              {__('Status', 'newsletter-builder')}
            </dt>
            <dd>
              {Status}
            </dd>
            <dt>
              {__('Campaign Name', 'newsletter-builder')}
            </dt>
            <dd>
              {Name}
            </dd>
            <dt>
              {__('Recipients', 'newsletter-builder')}
            </dt>
            <dd>
              {Recipients}
            </dd>
            <dt>
              {__('Total Opened', 'newsletter-builder')}
            </dt>
            <dd>
              {TotalOpened}
            </dd>
            <dt>
              {__('Unique Opened', 'newsletter-builder')}
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
            {__('Refresh', 'newsletter-builder')}
          </Button>
        </>
      ) : (
        <Spinner />
      )}
    </PluginDocumentSettingPanel>
  );
}
