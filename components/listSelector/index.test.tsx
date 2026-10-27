/**
 * @jest-environment jsdom
 */
import apiFetch from '@wordpress/api-fetch';
import { flush, render } from '@/test-utils';
import ListSelector from './index';

jest.mock('@wordpress/api-fetch', () => ({ __esModule: true, default: jest.fn() }));
jest.mock('@wordpress/components', () => jest.requireActual('@/test-utils').mockComponents);

const mockApiFetch = apiFetch as unknown as jest.Mock;

describe('ListSelector', () => {
  afterEach(() => {
    mockApiFetch.mockReset();
  });

  it.each([
    ['an empty array', []],
    ['an error object', { code: 'rest_error' }],
    ['null', null],
  ])('fetches lists once when the response is %s', async (_label, response) => {
    mockApiFetch.mockResolvedValue(response);

    const { rerender, unmount } = await render(
      <ListSelector selected="" updateFunction={() => {}} />,
    );
    await flush();
    await rerender(<ListSelector selected="" updateFunction={() => {}} />);
    await flush();

    expect(mockApiFetch).toHaveBeenCalledTimes(1);
    expect(mockApiFetch).toHaveBeenCalledWith({ path: '/wp-newsletter-builder/v1/lists' });
    unmount();
  });

  it('renders the fetched lists as options', async () => {
    mockApiFetch.mockResolvedValue([{ ListID: 'abc', Name: 'Daily' }]);

    const { container, unmount } = await render(
      <ListSelector selected="" updateFunction={() => {}} />,
    );
    await flush();

    expect(mockApiFetch).toHaveBeenCalledTimes(1);
    expect(container.querySelector('option[value="abc"]')?.textContent).toBe('Daily');
    unmount();
  });
});
