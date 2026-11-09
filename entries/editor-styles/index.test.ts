import { dispatch, select, subscribe } from '@wordpress/data';
import isThemeGlobalStyle from './is-theme-global-style';
import './index';

jest.mock('@wordpress/data', () => ({
  dispatch: jest.fn(),
  select: jest.fn(),
  subscribe: jest.fn(),
}));

const mockUpdateEditorSettings = jest.fn();
const listener = (subscribe as jest.Mock).mock.calls[0][0] as () => void;

const setStyles = (styles: object[]) => {
  (select as jest.Mock).mockReturnValue({ getEditorSettings: () => ({ styles }) });
};

// Global styles as the editor regenerates them for a block theme.
const regenerated = [
  { css: ':root{--wp--preset--color--base:#fff}', isGlobalStyles: true },
  { css: 'body{font-family:serif}', isGlobalStyles: true },
  { css: '', isGlobalStyles: true },
  { css: '', isGlobalStyles: true, __unstableType: 'svgs' },
];
const kept = [
  { css: '/* duotone */', isGlobalStyles: false, __unstableType: 'presets' },
  { css: '/* newsletter */', __unstableType: 'user' },
];

describe('editor styles', () => {
  beforeEach(() => {
    (dispatch as jest.Mock).mockReturnValue({ updateEditorSettings: mockUpdateEditorSettings });
  });

  afterEach(() => {
    mockUpdateEditorSettings.mockReset();
  });

  it('listens to the editor store', () => {
    expect(subscribe).toHaveBeenCalledWith(expect.any(Function), 'core/editor');
  });

  it('removes the untyped global styles a block theme adds', () => {
    setStyles([...kept, ...regenerated]);

    listener();

    expect(mockUpdateEditorSettings).toHaveBeenCalledWith({ styles: [...kept, regenerated[3]] });
  });

  it('does not update the settings when there is nothing to remove', () => {
    setStyles([...kept, regenerated[3]]);

    listener();

    expect(mockUpdateEditorSettings).not.toHaveBeenCalled();
  });

  it.each([
    [{ isGlobalStyles: true }, true],
    [{ isGlobalStyles: true, __unstableType: 'svgs' }, false],
    [{ isGlobalStyles: false }, false],
    [{ __unstableType: 'user' }, false],
  ])('isThemeGlobalStyle(%p) is %p', (style, expected) => {
    expect(isThemeGlobalStyle(style)).toBe(expected);
  });
});
