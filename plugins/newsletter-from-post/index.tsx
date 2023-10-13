/**
 * Sidebar for Newsletter Builder plugin for regular posts.
 */

import { registerPlugin } from '@wordpress/plugins';
import EmailSettings from './email-settings';

// Create a new Gutenberg sidebar
registerPlugin('newsletter-builder-plugin-sidebar', {
  icon: 'shield',
  render: () => (
    <EmailSettings />
  ),
});
