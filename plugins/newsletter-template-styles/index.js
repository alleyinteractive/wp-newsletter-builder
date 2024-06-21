import { registerPlugin } from '@wordpress/plugins';

import NewsletterTemplateStylesPanel from './newsletterStylesPanel';

registerPlugin('newsletter-template-styles', {
  render: NewsletterTemplateStylesPanel,
  icon: 'dashicons-art',
});
