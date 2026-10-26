/**
 * @jest-environment jsdom
 */
import apiFetch from '@wordpress/api-fetch';
import { flush, render } from '@/test-utils';
import useEmailLists from './index';

jest.mock('@wordpress/api-fetch', () => ({ __esModule: true, default: jest.fn() }));
jest.mock('@/hooks/useNewsletterMeta', () => ({
  __esModule: true,
  default: () => ({ meta: { list: ['abc'] } }),
}));

const mockApiFetch = apiFetch as unknown as jest.Mock;

let result: ReturnType<typeof useEmailLists>;

function TestComponent() {
  result = useEmailLists();
  return null;
}

describe('useEmailLists', () => {
  afterEach(() => {
    mockApiFetch.mockReset();
  });

  it.each([
    ['an empty array', []],
    ['an error object', { code: 'rest_error' }],
  ])('fetches lists once when the response is %s', async (_label, response) => {
    mockApiFetch.mockResolvedValue(response);

    const { rerender, unmount } = await render(<TestComponent />);
    await flush();
    await rerender(<TestComponent />);
    await flush();

    expect(mockApiFetch).toHaveBeenCalledTimes(1);
    expect(result.emailListOptions).toEqual([]);
    expect(result.listsLoaded).toBe(true);
    unmount();
  });

  it('is not loaded until the request settles', async () => {
    mockApiFetch.mockReturnValue(new Promise(() => {}));

    const { unmount } = await render(<TestComponent />);
    await flush();

    expect(result.listsLoaded).toBe(false);
    unmount();
  });

  it('is loaded with no lists when the request fails', async () => {
    mockApiFetch.mockRejectedValue(new Error('Request failed'));

    const { unmount } = await render(<TestComponent />);
    await flush();

    expect(result.emailListOptions).toEqual([]);
    expect(result.listsLoaded).toBe(true);
    unmount();
  });

  it('returns options and the selected list', async () => {
    mockApiFetch.mockResolvedValue([
      { ListID: 'abc', Name: 'Daily' },
      { ListID: 'def', Name: 'Weekly' },
    ]);

    const { unmount } = await render(<TestComponent />);
    await flush();

    expect(mockApiFetch).toHaveBeenCalledTimes(1);
    expect(result.emailListOptions).toHaveLength(2);
    expect(result.selectedEmailList).toEqual([{ label: 'Daily', value: 'abc' }]);
    unmount();
  });
});
