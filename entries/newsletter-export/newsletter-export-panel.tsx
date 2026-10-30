import apiFetch from '@wordpress/api-fetch';
import { Button, Flex } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

interface CoreEditor {
  getCurrentPostId: () => number;
  getCurrentPostAttribute: (attribute: string) => string;
  isEditedPostDirty: () => boolean;
}

interface ExportResponse {
  html: string;
  filename: string;
}

export default function NewsletterExportPanel() {
  const [busy, setBusy] = useState(false);
  const { createErrorNotice, createSuccessNotice } = useDispatch('core/notices');

  const { postId, isSaved, isDirty } = useSelect((select) => {
    const {
      getCurrentPostId,
      getCurrentPostAttribute,
      isEditedPostDirty,
    } = select('core/editor') as CoreEditor;

    return {
      postId: getCurrentPostId(),
      isSaved: getCurrentPostAttribute('status') !== 'auto-draft',
      isDirty: isEditedPostDirty(),
    };
  }, []);

  // The export renders the saved newsletter, so unsaved changes wouldn't be included.
  const canExport = isSaved && !isDirty;

  const fetchExport = () => apiFetch<ExportResponse>({
    path: `/wp-newsletter-builder/v1/export/${postId}`,
  });

  const onError = () => {
    createErrorNotice(
      __('The newsletter HTML could not be exported.', 'wp-newsletter-builder'),
      { type: 'snackbar' },
    );
  };

  const copyHtml = async () => {
    setBusy(true);
    const request = fetchExport();
    try {
      if (typeof ClipboardItem !== 'undefined' && navigator.clipboard?.write) {
        // Pass the clipboard a promise so Safari keeps the click's user activation.
        await navigator.clipboard.write([
          new ClipboardItem({
            'text/plain': request.then(({ html }) => new Blob([html], { type: 'text/plain' })),
          }),
        ]);
      } else {
        const { html } = await request;
        await navigator.clipboard.writeText(html);
      }
      createSuccessNotice(
        __('Newsletter HTML copied to the clipboard.', 'wp-newsletter-builder'),
        { type: 'snackbar' },
      );
    } catch (error) {
      // Tell a failed export apart from the browser blocking the clipboard.
      const exported = await request.then(() => true, () => false);
      if (exported) {
        createErrorNotice(
          __('Your browser blocked copying to the clipboard. Use Download .html instead.', 'wp-newsletter-builder'),
          { type: 'snackbar' },
        );
      } else {
        onError();
      }
    } finally {
      setBusy(false);
    }
  };

  const downloadHtml = async () => {
    setBusy(true);
    try {
      const { html, filename } = await fetchExport();
      const url = URL.createObjectURL(new Blob([html], { type: 'text/html' }));
      const link = document.createElement('a');
      link.href = url;
      link.download = filename;
      link.click();
      URL.revokeObjectURL(url);
    } catch (error) {
      onError();
    } finally {
      setBusy(false);
    }
  };

  return (
    <PluginDocumentSettingPanel
      name="newsletter-export"
      title={__('Export HTML', 'wp-newsletter-builder')}
    >
      {!isSaved ? (
        <p>{__('Save the newsletter to export its HTML.', 'wp-newsletter-builder')}</p>
      ) : null}
      {isSaved && isDirty ? (
        <p>{__('Save your changes to export the newsletter HTML.', 'wp-newsletter-builder')}</p>
      ) : null}
      <Flex justify="flex-start" wrap>
        <Button
          variant="secondary"
          onClick={copyHtml}
          disabled={!canExport || busy}
        >
          {__('Copy HTML', 'wp-newsletter-builder')}
        </Button>
        <Button
          variant="secondary"
          onClick={downloadHtml}
          disabled={!canExport || busy}
        >
          {__('Download .html', 'wp-newsletter-builder')}
        </Button>
      </Flex>
    </PluginDocumentSettingPanel>
  );
}
