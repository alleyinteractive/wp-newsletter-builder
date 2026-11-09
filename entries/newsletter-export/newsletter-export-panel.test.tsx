/**
 * @jest-environment jsdom
 */
import { act } from 'react';
import apiFetch from '@wordpress/api-fetch';
import { flush, render } from '@/test-utils';
import NewsletterExportPanel from './newsletter-export-panel';

jest.mock('@wordpress/api-fetch', () => ({ __esModule: true, default: jest.fn() }));
jest.mock('@wordpress/components', () => jest.requireActual('@/test-utils').mockComponents);
jest.mock('@wordpress/edit-post', () => ({
  PluginDocumentSettingPanel: ({ children }: { children: React.ReactNode }) => (
    <div>{children}</div>
  ),
}));

const mockCreateErrorNotice = jest.fn();
const mockCreateSuccessNotice = jest.fn();
const mockEditor = { status: 'draft', isDirty: false };

jest.mock('@wordpress/data', () => ({
  useDispatch: () => ({
    createErrorNotice: mockCreateErrorNotice,
    createSuccessNotice: mockCreateSuccessNotice,
  }),
  useSelect: (mapSelect: (select: () => object) => object) => mapSelect(() => ({
    getCurrentPostId: () => 123,
    getCurrentPostAttribute: () => mockEditor.status,
    isEditedPostDirty: () => mockEditor.isDirty,
  })),
}));

const mockApiFetch = apiFetch as unknown as jest.Mock;
const mockWriteText = jest.fn();

const getButton = (container: HTMLElement, label: string) => Array.from(container.querySelectorAll('button'))
  .find((button) => button.textContent === label) as HTMLButtonElement;

const click = async (button: HTMLButtonElement) => {
  await act(async () => {
    button.click();
  });
  await flush();
};

describe('NewsletterExportPanel', () => {
  beforeAll(() => {
    // jsdom has no clipboard or ClipboardItem, so the panel falls back to writeText().
    Object.defineProperty(navigator, 'clipboard', {
      value: { writeText: mockWriteText },
      configurable: true,
    });
  });

  afterEach(() => {
    jest.clearAllMocks();
    mockApiFetch.mockReset();
    mockWriteText.mockReset();
    mockEditor.status = 'draft';
    mockEditor.isDirty = false;
  });

  describe('buttons', () => {
    it('are disabled before the newsletter is saved', async () => {
      mockEditor.status = 'auto-draft';

      const { container, unmount } = await render(<NewsletterExportPanel />);

      expect(getButton(container, 'Copy HTML').disabled).toBe(true);
      expect(getButton(container, 'Download .html').disabled).toBe(true);
      expect(container.textContent).toContain('Save the newsletter to export its HTML.');
      unmount();
    });

    it('are disabled while there are unsaved changes', async () => {
      mockEditor.isDirty = true;

      const { container, unmount } = await render(<NewsletterExportPanel />);

      expect(getButton(container, 'Copy HTML').disabled).toBe(true);
      expect(getButton(container, 'Download .html').disabled).toBe(true);
      expect(container.textContent).toContain('Save your changes to export the newsletter HTML.');
      unmount();
    });

    it('are enabled when the newsletter is saved', async () => {
      const { container, unmount } = await render(<NewsletterExportPanel />);

      expect(getButton(container, 'Copy HTML').disabled).toBe(false);
      expect(getButton(container, 'Download .html').disabled).toBe(false);
      expect(container.querySelector('p')).toBeNull();
      unmount();
    });
  });

  describe('Copy HTML', () => {
    it('copies the exported HTML', async () => {
      mockApiFetch.mockResolvedValue({ html: '<p>Newsletter</p>', filename: 'newsletter.html' });
      mockWriteText.mockResolvedValue(undefined);

      const { container, unmount } = await render(<NewsletterExportPanel />);
      await click(getButton(container, 'Copy HTML'));

      expect(mockApiFetch).toHaveBeenCalledWith({ path: '/wp-newsletter-builder/v1/export/123' });
      expect(mockWriteText).toHaveBeenCalledWith('<p>Newsletter</p>');
      expect(mockCreateSuccessNotice).toHaveBeenCalledWith('Newsletter HTML copied to the clipboard.', { type: 'snackbar' });
      expect(mockCreateErrorNotice).not.toHaveBeenCalled();
      unmount();
    });

    it('says the browser blocked copying when the export succeeded', async () => {
      mockApiFetch.mockResolvedValue({ html: '<p>Newsletter</p>', filename: 'newsletter.html' });
      mockWriteText.mockRejectedValue(new Error('NotAllowedError'));

      const { container, unmount } = await render(<NewsletterExportPanel />);
      await click(getButton(container, 'Copy HTML'));

      expect(mockCreateErrorNotice).toHaveBeenCalledWith(
        'Your browser blocked copying to the clipboard. Use Download .html instead.',
        { type: 'snackbar' },
      );
      expect(mockCreateSuccessNotice).not.toHaveBeenCalled();
      unmount();
    });

    it('says the export failed when the request fails', async () => {
      mockApiFetch.mockRejectedValue({ code: 'render_failed' });

      const { container, unmount } = await render(<NewsletterExportPanel />);
      await click(getButton(container, 'Copy HTML'));

      expect(mockWriteText).not.toHaveBeenCalled();
      expect(mockCreateErrorNotice).toHaveBeenCalledWith('The newsletter HTML could not be exported.', { type: 'snackbar' });
      unmount();
    });
  });

  describe('Download .html', () => {
    it('says the export failed when the request fails', async () => {
      mockApiFetch.mockRejectedValue({ code: 'render_failed' });

      const { container, unmount } = await render(<NewsletterExportPanel />);
      await click(getButton(container, 'Download .html'));

      expect(mockCreateErrorNotice).toHaveBeenCalledWith('The newsletter HTML could not be exported.', { type: 'snackbar' });
      unmount();
    });
  });
});
