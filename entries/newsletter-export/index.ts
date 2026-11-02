import { registerPlugin } from '@wordpress/plugins';
import NewsletterExportPanel from '@/entries/newsletter-export/newsletter-export-panel';

registerPlugin('newsletter-export', {
  render: NewsletterExportPanel,
});
