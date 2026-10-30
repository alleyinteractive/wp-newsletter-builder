import { dispatch, select } from '@wordpress/data';
import setTemplateVariables from './set-template-variables';

jest.mock('@wordpress/data', () => ({
  dispatch: jest.fn(),
  select: jest.fn(),
}));

const mockUpdateEditorSettings = jest.fn();

describe('setTemplateVariables', () => {
  beforeEach(() => {
    (dispatch as jest.Mock).mockReturnValue({ updateEditorSettings: mockUpdateEditorSettings });
  });

  it('replaces the template variables style and keeps the others', () => {
    const otherStyle = { css: '/* other */', __unstableType: 'presets' };
    (select as jest.Mock).mockReturnValue({
      getEditorSettings: () => ({
        styles: [
          { css: ':root {--template-font-family: Arial;}', id: 'wp-newsletter-builder-template-variables', __unstableType: 'user' },
          otherStyle,
        ],
      }),
    });

    setTemplateVariables({ bgColor: '#eeeeee', fontStack: 'Georgia, serif', linkColor: '#ff0000' });

    expect(mockUpdateEditorSettings).toHaveBeenCalledWith({
      styles: [
        otherStyle,
        {
          css: ':root {--template-font-family: Georgia, serif; --template-bg-color: #eeeeee; --template-link-color: #ff0000;}',
          id: 'wp-newsletter-builder-template-variables',
          __unstableType: 'user',
        },
      ],
    });
  });
});
