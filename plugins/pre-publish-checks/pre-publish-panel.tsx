import { PluginPrePublishPanel } from '@wordpress/edit-post';

function PrePublishPanel() {
  return (
    <PluginPrePublishPanel>
      <h2>Pre Publish Panel</h2>
      <p>Pre Publish Panel content</p>
    </PluginPrePublishPanel>
  );
}

export default PrePublishPanel;
