import React from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

// @ts-ignore
export default function NewsletterTemplateStylesPanel() {
  return (
    <PluginDocumentSettingPanel
      name="newsletter-template-styles"
      title="Template Styles"
      className="newsletter-template-styles-panel"
    >
      <p>This is my template style panel</p>
    </PluginDocumentSettingPanel>
  );
}
