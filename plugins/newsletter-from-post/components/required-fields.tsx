import { __ } from '@wordpress/i18n';
import { NewsletterMeta } from '@/hooks/useNewsletterMeta';

interface RequiredFieldsProps {
  meta: Pick<NewsletterMeta, 'type' | 'template' | 'fromName' | 'subject' | 'preview' | 'list'>
  postTitle: string;
  postExcerpt: string;
}

function RequiredFields({ meta, postTitle, postExcerpt }: RequiredFieldsProps) {
  return (
    <>
      {!meta.type ? (
        <p style={{ color: 'red' }}>{__('Header Type is Required', 'wp-newsletter-builder')}</p>
      ) : null}
      {!meta.template ? (
        <p style={{ color: 'red' }}>{__('Template is Required', 'wp-newsletter-builder')}</p>
      ) : null}
      {!meta.fromName ? (
        <p style={{ color: 'red' }}>{__('From Name is Required', 'wp-newsletter-builder')}</p>
      ) : null}
      {!meta.subject && !postTitle ? (
        <p style={{ color: 'red' }}>{__('Subject is Required', 'wp-newsletter-builder')}</p>
      ) : null}
      {!meta.preview && !postExcerpt ? (
        <p style={{ color: 'red' }}>{__('Preview Text is Required', 'wp-newsletter-builder')}</p>
      ) : null}
      {meta.list.length === 0 ? (
        <p style={{ color: 'red' }}>{__('Email List is Required', 'wp-newsletter-builder')}</p>
      ) : null}
    </>
  );
}

export default RequiredFields;
