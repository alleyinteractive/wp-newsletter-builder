import { __ } from '@wordpress/i18n';

interface InvalidTemplateProps {
  invalid: boolean;
}

function InvalidTemplate({ invalid }: InvalidTemplateProps) {
  return (
    <>
      {invalid ? (
        <p style={{ color: 'red' }}>{__('Invalid Template: Template must have at least one Newsletter Single Post Block.', 'wp-newsletter-builder')}</p>
      ) : null}
    </>
  );
}

export default InvalidTemplate;
