import { PluginPrePublishPanel } from '@wordpress/edit-post';
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

function PrePublishPanel() {
  // @ts-ignore
  const postTitle = useSelect((select) => select('core/editor').getEditedPostAttribute('title'));
  const isTitleOk = postTitle.length > 0;

  const { lockPostSaving, unlockPostSaving } = useDispatch('core/editor');
  const { createWarningNotice, removeNotice } = useDispatch('core/notices');

  if (!isTitleOk) {
    lockPostSaving('title-empty-lock');
    createWarningNotice(
      __('Please enter a newsletter title before publishing.', 'wp-newsletter-builder'),
      { id: 'title-empty-lock', isDismissible: false },
    );
  } else {
    unlockPostSaving('title-empty-lock');
    removeNotice('title-empty-lock');
  }

  return (
    <PluginPrePublishPanel
      title={__('Newsletter Publish Requirements', 'wp-newsletter-builder')}
      initialOpen
    >
      <p>
        {isTitleOk
          ? __('All headline requirements are met.', 'wp-newsletter-builder')
          : __('Headline is required before publishing.', 'wp-newsletter-builder')}
      </p>
    </PluginPrePublishPanel>
  );
}

export default PrePublishPanel;
