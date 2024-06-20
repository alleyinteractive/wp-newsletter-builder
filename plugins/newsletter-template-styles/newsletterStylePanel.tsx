import React, { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
// import { useEntityProp } from '@wordpress/core-data';
import { usePostMetaValue } from '@alleyinteractive/block-editor-tools';
import { ColorPicker, SelectControl } from '@wordpress/components';

// @ts-ignore
export default function NewsletterTemplateStylesPanel() {
  const [bgColor, setBgColor] = useState('#fff');
  const [linkColor, setLinkColor] = useState('#0073aa');

  const emailSafeFonts = [
    { label: 'Arial', value: 'Arial, sans serif' },
    { label: 'Courier New', value: 'Courier New, monospace' },
    { label: 'Georgia', value: 'Georgia, serif' },
    { label: 'Impact', value: 'Impact, sans-serif' },
    { label: 'Lucida Sans Unicode', value: 'Lucida Sans Unicode, sans-serif' },
    { label: 'Tahoma', value: 'Tahoma, sans serif' },
    { label: 'Times New Roman', value: 'Times New Roman, serif' },
    { label: 'Trebuchet MS', value: 'Trebuchet MS, sans-serif' },
    { label: 'Verdana', value: 'Verdana, sans serif' },
  ];

  // const postId = select('core/editor').getCurrentPostId();
  const [fontStack, setFontStack] = usePostMetaValue('nb_template_font');

  return (
    <PluginDocumentSettingPanel
      name="newsletter-template-styles"
      title="Template Styles"
      className="newsletter-template-styles-panel"
    >
      <h3>{__('Background color', 'wp-newsletter-builder')}</h3>
      <ColorPicker
        color={bgColor}
        onChange={(color) => setBgColor(color)}
      />
      <h3>{__('Link color', 'wp-newsletter-builder')}</h3>
      <ColorPicker
        color={linkColor}
        onChange={(color) => setLinkColor(color)}
      />
      <h3>{__('Font family', 'wp-newsletter-builder')}</h3>
      <SelectControl
        value={fontStack || 'Arial, sans-serif'}
        options={emailSafeFonts}
        onChange={setFontStack}
      />

    </PluginDocumentSettingPanel>
  );
}
