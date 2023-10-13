import { registerPlugin } from '@wordpress/plugins';

import NewsletterStatusPanel from './newsletterStatusPanel';

registerPlugin('newsletter-status', {
  render: NewsletterStatusPanel,
});
