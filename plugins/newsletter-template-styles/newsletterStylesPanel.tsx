import React from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { usePostMetaValue } from '@alleyinteractive/block-editor-tools';
import { ColorPicker, SelectControl } from '@wordpress/components';

import setTemplateVariables from './set-template-variables';

// @ts-ignore
export default function NewsletterTemplateStylesPanel() {
  const [bgColor, setBgColor] = usePostMetaValue('nb_template_bg_color');
  const [fontStack, setFontStack] = usePostMetaValue('nb_template_font');
  const [linkColor, setLinkColor] = usePostMetaValue('nb_template_link_color');

  const defaultValues = {
    bgColor: '#fefefe',
    fontStack: 'Arial, sans-serif',
    linkColor: '#0073aa',
  };

  // The editor loads with the saved values, so this only updates it after a change.
  const updateEditor = (changes: Partial<typeof defaultValues>) => {
    setTemplateVariables({
      bgColor: bgColor || defaultValues.bgColor,
      fontStack: fontStack || defaultValues.fontStack,
      linkColor: linkColor || defaultValues.linkColor,
      ...changes,
    });
  };

  const emailSafeFonts = [
    { label: 'Arial', value: 'Arial, sans-serif' },
    { label: 'Courier New', value: 'Courier New, monospace' },
    { label: 'Georgia', value: 'Georgia, serif' },
    { label: 'Helvetica', value: 'Helvetica, sans-serif' },
    { label: 'Lucida Sans Unicode', value: 'Lucida Sans Unicode, sans-serif' },
    { label: 'Tahoma', value: 'Tahoma, sans-serif' },
    { label: 'Times New Roman', value: 'Times New Roman, serif' },
    { label: 'Trebuchet MS', value: 'Trebuchet MS, sans-serif' },
    { label: 'Verdana', value: 'Verdana, sans-serif' },
  ];

  return (
    <PluginDocumentSettingPanel
      name="newsletter-template-styles"
      title="Template Styles"
      className="newsletter-template-styles-panel"
    >
      <h3>{__('Background color', 'wp-newsletter-builder')}</h3>
      <ColorPicker
        color={bgColor || defaultValues.bgColor}
        onChange={(color) => {
          setBgColor(color);
          updateEditor({ bgColor: color });
        }}
      />
      <h3>{__('Link color', 'wp-newsletter-builder')}</h3>
      <ColorPicker
        color={linkColor || defaultValues.linkColor}
        onChange={(color) => {
          setLinkColor(color);
          updateEditor({ linkColor: color });
        }}
      />
      <h3>{__('Font family', 'wp-newsletter-builder')}</h3>
      <SelectControl
        value={fontStack || defaultValues.fontStack}
        options={emailSafeFonts}
        onChange={(font) => {
          setFontStack(font);
          updateEditor({ fontStack: font });
        }}
      />
    </PluginDocumentSettingPanel>
  );
}
