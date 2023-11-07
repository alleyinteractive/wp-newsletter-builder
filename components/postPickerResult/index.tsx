import { SafeHtml } from '@alleyinteractive/block-editor-tools';
import type { WP_REST_API_Search_Results } from 'wp-types'; // eslint-disable-line camelcase

import './index.scss';

interface PostPickerResultProps extends WP_REST_API_Search_Results { // eslint-disable-line camelcase, max-len
  featured_image: string;
  post_date: string;
  title: string;
}

function PostPickerResult({
  featured_image: featuredImage,
  post_date: postDate,
  title,
}: PostPickerResultProps) {
  return (
    <div className="nb-post-picker-result">
      <div className="nb-post-picker-result-image__container">
        {featuredImage ? (
          <img
            className="nb-post-picker-result-image"
            src={featuredImage}
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
        {postDate}
      </span>
    </div>
  );
}

export default PostPickerResult;
