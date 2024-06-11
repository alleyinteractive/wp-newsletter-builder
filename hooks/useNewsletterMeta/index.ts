import { usePostMeta } from '@alleyinteractive/block-editor-tools';

export interface NewsletterMeta {
  type: string;
  template: string;
  fromName: string;
  subject: string;
  preview: string;
  list: string[];
  image: number;
  send: boolean;
  content: string;
  sentBreakingPostId: number[];
}

function useNewsletterMeta() {
  const [meta, setMeta] = usePostMeta();

  const {
    nb_breaking_email_type: type = '',
    nb_breaking_template: template = '',
    nb_breaking_from_name: fromName = '',
    nb_breaking_subject: subject = '',
    nb_breaking_preview: preview = '',
    nb_breaking_list: list = [],
    nb_breaking_header_img: image = 0, // eslint-disable-line
    nb_breaking_should_send: send = false,
    nb_breaking_content: content = '', // eslint-disable-line
    nb_newsletter_sent_breaking_post_id: sentBreakingPostId = [],
  } = meta;

  return {
    meta: {
      type,
      template,
      fromName,
      subject,
      preview,
      list,
      image,
      send,
      content,
      sentBreakingPostId,
    },
    setMeta,
  };
}

export default useNewsletterMeta;
