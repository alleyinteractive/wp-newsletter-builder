import { registerPlugin } from '@wordpress/plugins';
import PrePublishPanel from '@/plugins/pre-publish-checks/pre-publish-panel';

registerPlugin('pre-publish-checks', {
  render: PrePublishPanel,
});
