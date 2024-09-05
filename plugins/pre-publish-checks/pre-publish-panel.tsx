import { PluginPrePublishPanel } from '@wordpress/edit-post';
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { usePostMetaValue } from '@alleyinteractive/block-editor-tools';

const SUPPRESSION_ID_LOCK = 'suppression-group-id-lock';
const TITLE_LOCK = 'title-empty-lock';

function PrePublishPanel() {
  // @ts-ignore
  const postTitle = useSelect((select) => select('core/editor').getEditedPostAttribute('title'));
  const [supressionGroupId] = usePostMetaValue('nb_newsletter_suppression_group');
  const isTitleOk = postTitle.trim().length > 0;

  const { lockPostSaving, unlockPostSaving } = useDispatch('core/editor');
  const { createWarningNotice, removeNotice } = useDispatch('core/notices');

  if (!isTitleOk) {
    lockPostSaving(TITLE_LOCK);
    createWarningNotice(
      __('Please enter a newsletter title before publishing.', 'wp-newsletter-builder'),
      { id: TITLE_LOCK, isDismissible: false },
    );
  } else {
    unlockPostSaving(TITLE_LOCK);
    removeNotice(TITLE_LOCK);
  }

  if (!supressionGroupId) {
    lockPostSaving(SUPPRESSION_ID_LOCK);
    createWarningNotice(
      __('Please select a suppression group before publishing.', 'wp-newsletter-builder'),
      { id: SUPPRESSION_ID_LOCK, isDismissible: false },
    );
  } else {
    unlockPostSaving(SUPPRESSION_ID_LOCK);
    removeNotice(SUPPRESSION_ID_LOCK);
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
      <p>
        {supressionGroupId
          ? __('Suppression group selected.', 'wp-newsletter-builder')
          : __('Suppression group is required before publishing.', 'wp-newsletter-builder')}
      </p>
    </PluginPrePublishPanel>
  );
}

export default PrePublishPanel;
