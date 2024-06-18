import { registerPlugin } from '@wordpress/plugins';

import NewsletterTemplateStylesPanel from './newsletterStylePanel';

registerPlugin('newsletter-status', {
  render: NewsletterTemplateStylesPanel,
  icon: 'dashicons-art',
});
