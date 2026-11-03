import { dispatch, select, subscribe } from '@wordpress/data';
import isThemeGlobalStyle, { EditorStyle } from './is-theme-global-style';

interface CoreEditor {
  getEditorSettings: () => { styles?: EditorStyle[] };
}

interface CoreEditorActions {
  updateEditorSettings: (settings: { styles: EditorStyle[] }) => void;
}

// With a block theme, the editor regenerates the theme's global styles in the browser,
// which undoes the server-side removal in filter_newsletter_editor_styles(). Remove
// them again whenever they're added.
subscribe(() => {
  const { styles = [] } = (select('core/editor') as unknown as CoreEditor).getEditorSettings();

  // Only update when there's something to remove, since the update notifies this listener.
  if (styles.some(isThemeGlobalStyle)) {
    (dispatch('core/editor') as unknown as CoreEditorActions).updateEditorSettings({
      styles: styles.filter((style) => !isThemeGlobalStyle(style)),
    });
  }
}, 'core/editor');
