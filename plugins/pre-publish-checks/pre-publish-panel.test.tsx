/**
 * @jest-environment jsdom
 */
import { render } from '@/test-utils';
import PrePublishPanel from './pre-publish-panel';

jest.mock('@wordpress/edit-post', () => ({
  PluginPrePublishPanel: ({ children }: { children: React.ReactNode }) => <div>{children}</div>,
}));

const mockLockPostSaving = jest.fn();
const mockUnlockPostSaving = jest.fn();
const mockCreateWarningNotice = jest.fn();
const mockRemoveNotice = jest.fn();
let mockSuppressionGroup = '';

jest.mock('@wordpress/data', () => ({
  useDispatch: () => ({
    lockPostSaving: mockLockPostSaving,
    unlockPostSaving: mockUnlockPostSaving,
    createWarningNotice: mockCreateWarningNotice,
    removeNotice: mockRemoveNotice,
  }),
  useSelect: (mapSelect: (select: () => object) => unknown) => mapSelect(() => ({
    getEditedPostAttribute: () => 'My Newsletter',
  })),
}));
// Virtual, since the package only exports an `import` condition, which Jest can't resolve.
jest.mock('@alleyinteractive/block-editor-tools', () => ({
  usePostMetaValue: () => [mockSuppressionGroup],
}), { virtual: true });

const LOCK = 'suppression-group-id-lock';
const testWindow = window as unknown as { newsletterBuilder?: { usesSuppressionLists?: unknown } };

describe('PrePublishPanel suppression group check', () => {
  afterEach(() => {
    jest.clearAllMocks();
    mockSuppressionGroup = '';
    delete testWindow.newsletterBuilder;
  });

  it.each([
    ['no settings are localized', undefined],
    // wp_localize_script() passes false as ''.
    ['the provider does not use suppression lists', { usesSuppressionLists: '' }],
  ])('does not lock publishing when %s', async (_label, settings) => {
    testWindow.newsletterBuilder = settings;

    const { container, unmount } = await render(<PrePublishPanel />);

    expect(mockLockPostSaving).not.toHaveBeenCalledWith(LOCK);
    expect(mockUnlockPostSaving).toHaveBeenCalledWith(LOCK);
    expect(mockRemoveNotice).toHaveBeenCalledWith(LOCK);
    expect(container.textContent).not.toContain('Suppression group');
    unmount();
  });

  describe('when the provider uses suppression lists', () => {
    beforeEach(() => {
      // wp_localize_script() passes true as '1'.
      testWindow.newsletterBuilder = { usesSuppressionLists: '1' };
    });

    it('locks publishing until a suppression group is selected', async () => {
      const { container, unmount } = await render(<PrePublishPanel />);

      expect(mockLockPostSaving).toHaveBeenCalledWith(LOCK);
      expect(mockCreateWarningNotice).toHaveBeenCalledWith(
        'Please select a suppression group before publishing.',
        { id: LOCK, isDismissible: false },
      );
      expect(container.textContent).toContain('Suppression group is required before publishing.');
      unmount();
    });

    it('allows publishing once a suppression group is selected', async () => {
      mockSuppressionGroup = '42';

      const { container, unmount } = await render(<PrePublishPanel />);

      expect(mockLockPostSaving).not.toHaveBeenCalledWith(LOCK);
      expect(mockUnlockPostSaving).toHaveBeenCalledWith(LOCK);
      expect(container.textContent).toContain('Suppression group selected.');
      unmount();
    });
  });
});
