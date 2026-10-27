/**
 * @jest-environment jsdom
 */
import apiFetch from '@wordpress/api-fetch';
import { flush, render } from '@/test-utils';
import Edit from './edit';

jest.mock('@wordpress/api-fetch', () => ({ __esModule: true, default: jest.fn() }));
jest.mock('@wordpress/components', () => jest.requireActual('@/test-utils').mockComponents);
jest.mock('@wordpress/data', () => ({
  dispatch: jest.fn(),
  useSelect: () => ({}),
}));
jest.mock('@wordpress/blocks', () => ({
  parse: jest.fn(),
}));
jest.mock('@wordpress/block-editor', () => ({
  useBlockProps: () => ({}),
}));
jest.mock('../../hooks/usePostMeta', () => ({
  __esModule: true,
  default: () => [{ nb_newsletter_list: [] }, jest.fn()],
}));
jest.mock('../../components/emailTypeSelector', () => ({
  __esModule: true,
  default: () => null,
}));
jest.mock('./index.scss', () => ({}));

const mockApiFetch = apiFetch as unknown as jest.Mock;
const testWindow = window as unknown as { newsletterBuilder?: { usesSuppressionLists: boolean } };

const callsTo = (path: string) => mockApiFetch.mock.calls
  .filter(([options]) => options.path === `/wp-newsletter-builder/v1/${path}`).length;

describe('Email settings block', () => {
  afterEach(() => {
    mockApiFetch.mockReset();
    delete testWindow.newsletterBuilder;
  });

  it.each([
    ['an empty array', []],
    ['an error object', { code: 'rest_error' }],
  ])('fetches each list once when the response is %s', async (_label, response) => {
    testWindow.newsletterBuilder = { usesSuppressionLists: true };
    mockApiFetch.mockResolvedValue(response);

    const { rerender, unmount } = await render(<Edit />);
    await flush();
    await rerender(<Edit />);
    await flush();

    expect(callsTo('lists')).toBe(1);
    expect(callsTo('suppression-lists')).toBe(1);
    expect(mockApiFetch).toHaveBeenCalledTimes(2);
    unmount();
  });

  it('does not fetch suppression lists when the provider does not use them', async () => {
    mockApiFetch.mockResolvedValue([]);

    const { unmount } = await render(<Edit />);
    await flush();

    expect(callsTo('lists')).toBe(1);
    expect(callsTo('suppression-lists')).toBe(0);
    unmount();
  });
});
