import { SafeHtml } from '@alleyinteractive/block-editor-tools';
import type { WP_REST_API_Search_Results } from 'wp-types';

import './index.scss';

interface PostPickerResultProps extends WP_REST_API_Search_Results {
  featured_image: string;
  post_date: string;
  title: string;
}

function PostPickerResult({
  featured_image,
  post_date,
  title,
}: PostPickerResultProps) {
  return (
    <div className="nb-post-picker-result">
      <div className="nb-post-picker-result-image__container">
        {featured_image ? (
          <img
            className="nb-post-picker-result-image"
            src={featured_image}
            alt=""
          />
        ) : (
          null
        )}
      </div>
      <SafeHtml
        html={title}
        className="nb-post-picker-result-title"
        tag="div"
      />
      <span className="nb-post-picker-result-date">
        {post_date}
      </span>
    </div>
  );
}

export default PostPickerResult;
