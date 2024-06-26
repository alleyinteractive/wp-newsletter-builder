import { usePostMeta } from '@alleyinteractive/block-editor-tools';
import { useCallback } from '@wordpress/element';

export interface NewsletterMeta {
  type: string;
  template: number;
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
    nb_breaking_template: template = 0,
    nb_breaking_from_name: fromName = '',
    nb_breaking_subject: subject = '',
    nb_breaking_preview: preview = '',
    nb_breaking_list: list = [],
    nb_breaking_header_img: image = 0,
    nb_breaking_should_send: send = false,
    nb_breaking_content: content = '',
    nb_newsletter_sent_breaking_post_id: sentBreakingPostId = [],
  } = meta;

  const resetTemplate = useCallback(() => {
    setMeta({
      nb_breaking_template: 0,
      nb_breaking_content: '',
      nb_breaking_subject: '',
      nb_breaking_preview: '',
      nb_breaking_list: [],
      nb_breaking_should_send: false,
    });
  }, [setMeta]);

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
    resetTemplate,
    setMeta,
  };
}

export default useNewsletterMeta;
