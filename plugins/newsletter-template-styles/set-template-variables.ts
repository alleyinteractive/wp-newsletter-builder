import { dispatch, select } from '@wordpress/data';

interface EditorStyle {
  css?: string;
  id?: string;
  __unstableType?: string;
}

interface TemplateVariables {
  bgColor: string;
  fontStack: string;
  linkColor: string;
}

// Matches the style added by filter_newsletter_editor_styles().
const STYLE_ID = 'wp-newsletter-builder-template-variables';

/**
 * Updates the template's font and colors in the editor.
 *
 * The editor canvas is an iframe, so the variables are set with an editor style.
 *
 * @param {TemplateVariables} variables The template's font and colors.
 */
const setTemplateVariables = ({ bgColor, fontStack, linkColor }: TemplateVariables) => {
  const { getEditorSettings } = select('core/editor') as unknown as {
    getEditorSettings: () => { styles?: EditorStyle[] };
  };
  const { updateEditorSettings } = dispatch('core/editor') as unknown as {
    updateEditorSettings: (settings: { styles: EditorStyle[] }) => void;
  };
  const { styles = [] } = getEditorSettings();

  updateEditorSettings({
    styles: [
      ...styles.filter((style) => style.id !== STYLE_ID),
      {
        css: `:root {--template-font-family: ${fontStack}; --template-bg-color: ${bgColor}; --template-link-color: ${linkColor};}`,
        id: STYLE_ID,
        __unstableType: 'user',
      },
    ],
  });
};

export default setTemplateVariables;
