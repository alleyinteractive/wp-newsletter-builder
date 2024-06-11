/**
 * SentNewsletter component
 */

import {
  Button,
  PanelRow,
} from '@wordpress/components';
import type { WP_REST_API_Post } from 'wp-types'; // eslint-disable-line camelcase

import { usePost } from '@alleyinteractive/block-editor-tools';

interface SentNewsletterProps {
  postId: number;
}

interface Post extends WP_REST_API_Post { // eslint-disable-line camelcase
  meta: {
    nb_newsletter_subject?: string;
  };
}

function SentNewsletter({
  postId,
}: SentNewsletterProps) {
  const post = usePost(postId, 'nb_newsletter') as Post;
  if (!post) {
    return null;
  }

  const {
    meta: {
      nb_newsletter_subject: title = '',
    } = {},
    link = '',
  } = post;

  return (
    <PanelRow>
      {link && title ? (
        <Button
          variant="link"
          href={link}
          target="_blank"
          style={{ marginTop: '1rem' }}
        >
          {title}
        </Button>
      ) : null}
    </PanelRow>
  );
}

export default SentNewsletter;
