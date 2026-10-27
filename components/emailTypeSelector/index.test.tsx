/**
 * @jest-environment jsdom
 */
import apiFetch from '@wordpress/api-fetch';
import { flush, render } from '@/test-utils';
import EmailTypeSelector from './index';

jest.mock('@wordpress/api-fetch', () => ({ __esModule: true, default: jest.fn() }));
jest.mock('@wordpress/components', () => jest.requireActual('@/test-utils').mockComponents);

const mockApiFetch = apiFetch as unknown as jest.Mock;

const noop = () => {};
const props = {
  contentHandler: noop,
  typeHandler: noop,
  imageHandler: noop,
  templateHandler: noop,
  fromNameHandler: noop,
  typeValue: '',
  templateValue: '',
  fromNameValue: '',
};

describe('EmailTypeSelector', () => {
  afterEach(() => {
    mockApiFetch.mockReset();
  });

  it.each([
    // PHP encodes an empty settings array as [].
    ['an empty array', []],
    ['an empty object', {}],
    ['null', null],
  ])('fetches email types once when the response is %s', async (_label, response) => {
    mockApiFetch.mockResolvedValue(response);

    const { rerender, unmount } = await render(<EmailTypeSelector {...props} />);
    await flush();
    await rerender(<EmailTypeSelector {...props} />);
    await flush();

    expect(mockApiFetch).toHaveBeenCalledTimes(1);
    expect(mockApiFetch).toHaveBeenCalledWith({ path: '/wp-newsletter-builder/v1/email-types' });
    unmount();
  });

  it('renders the fetched types as options', async () => {
    mockApiFetch.mockResolvedValue({
      daily: {
        label: 'Daily', image: '', templates: [], ad_tags: [], from_name: '',
      },
    });

    const { container, unmount } = await render(<EmailTypeSelector {...props} />);
    await flush();

    expect(mockApiFetch).toHaveBeenCalledTimes(1);
    expect(container.querySelector('option[value="daily"]')?.textContent).toBe('Daily');
    unmount();
  });

  describe('From Name', () => {
    afterEach(() => {
      delete (globalThis as any).newsletterBuilder;
    });

    it('is shown when the provider sends email', async () => {
      mockApiFetch.mockResolvedValue({});

      const { container, unmount } = await render(<EmailTypeSelector {...props} />);
      await flush();

      // Header Type and From Name.
      expect(container.querySelectorAll('select')).toHaveLength(2);
      unmount();
    });

    it('is hidden when the provider does not send email', async () => {
      // wp_localize_script() passes false as ''.
      (globalThis as any).newsletterBuilder = { sendsEmail: '' };
      mockApiFetch.mockResolvedValue({});

      const { container, unmount } = await render(<EmailTypeSelector {...props} />);
      await flush();

      // Header Type only.
      expect(container.querySelectorAll('select')).toHaveLength(1);
      unmount();
    });
  });
});
