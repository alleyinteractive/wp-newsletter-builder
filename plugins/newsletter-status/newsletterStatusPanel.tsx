import React from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import { Button } from '@wordpress/components';
import NewsletterSpinner from '@/components/newsletterSpinner';
import useNewsletterStatus from '@/hooks/useNewsletterStats';

export default function NewsletterStatusPanel() {
  // @ts-ignore
  const postId = select('core/editor').getCurrentPostId();
  const {
    stats,
    fetching,
    fetchStats,
    validStats,
  } = useNewsletterStatus(postId);

  const {
    Status: statusString = '',
    Name = '',
    Recipients = '',
    TotalOpened = '',
    UniqueOpened = '',
  } = stats;

  if (!validStats && !fetching) {
    return (
      <PluginDocumentSettingPanel
        name="rubric-selection"
        title={__('Newsletter Stats', 'wp-newsletter-builder')}
      >
        <p>
          {__('Newsletter stats not available. Try clicking the Refresh button.', 'wp-newsletter-builder')}
        </p>
        <Button
          onClick={fetchStats}
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
      title={__('Newsletter Stats', 'wp-newsletter-builder')}
    >
      {validStats ? (
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
            onClick={fetchStats}
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
