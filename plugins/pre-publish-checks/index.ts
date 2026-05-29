import { registerPlugin } from '@wordpress/plugins';
import PrePublishPanel from './pre-publish-panel';

registerPlugin('pre-publish-checks', {
  render: PrePublishPanel,
});
