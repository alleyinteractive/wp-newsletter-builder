import { useCallback, useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { __, sprintf } from '@wordpress/i18n';
import { Button, TextControl, Spinner } from '@wordpress/components';
import type { WP_REST_API_Search_Results, WP_REST_API_Search_Result } from 'wp-types'; // eslint-disable-line camelcase

import './post-list.scss';

interface PostListProps {
  baseUrl: string;
  searchRender: (post: object) => JSX.Element;
  selected?: WP_REST_API_Search_Result[]; // eslint-disable-line camelcase
  setSelected: (post: WP_REST_API_Search_Result) => void; // eslint-disable-line camelcase
}

interface Params {
  searchValue: string;
  page: number;
}

/**
 * Displays a list of posts in the post picker modal.
 *
 * @param {obj} atts The attributes of the PostList.
 */
function PostList({
  baseUrl,
  searchRender,
  selected,
  setSelected,
}: PostListProps) {
  const [isUpdating, setIsUpdating] = useState(false);
  const [listposts, setListposts] = useState<WP_REST_API_Search_Results>([]); // eslint-disable-line camelcase, max-len
  const [initialLoad, setInitialLoad] = useState(false);
  const [totalPages, setTotalPages] = useState(0);
  const [pathParams, setPathParams] = useState({
    searchValue: '',
    page: 1,
  });

  /**
   * Gets the posts based on the params.
   *
   * @param {object} params The parameters.
   * @param {bool} cancelled Whether the useEffect has been cancelled.
   * @param {bool} overrideCache Whether we should override the cache.
   */
  const getPosts = useCallback(async (params: Params, cancelled: Boolean = false) => {
    /**
     * Gets the api path.
     *
     * @param {*} params The parameters.
     */
    function getPath() {
      let path = addQueryArgs(
        baseUrl,
        {
          page: params.page,
        },
      );
      if (params.searchValue && params.searchValue.length > 2) {
        path = addQueryArgs(
          path,
          {
            search: params.searchValue,
          },
        );
      }
      return path;
    }

    if (params.searchValue && params.searchValue.length <= 2) {
      return;
    }
    const path = getPath();
    setIsUpdating(true);
    const response = await apiFetch({ path, parse: false });
    setTotalPages(parseInt(
      // @ts-ignore
      response.headers.get('X-WP-TotalPages'),
      10,
    ));
    // @ts-ignore
    const result = await response.json();
    let posts = result as any as WP_REST_API_Search_Results; // eslint-disable-line camelcase
    if (params.page > 1) {
      posts = [
        ...listposts,
        ...result as any as WP_REST_API_Search_Results, // eslint-disable-line camelcase
      ];
    }
    if (cancelled) {
      return;
    }
    // @ts-ignore
    setListposts(posts as any as WP_REST_API_Search_Results); // eslint-disable-line camelcase
    setIsUpdating(false);
  }, [listposts, baseUrl]);

  /**
   * Loads more posts.
   */
  const loadMore = () => {
    const newParams = {
      ...pathParams,
      page: pathParams.page + 1,
    };
    setPathParams(newParams);
    getPosts(newParams);
  };

  /**
   * Handles a change to the search text string.
   * @param {event} event - The event from typing in the text box.
   */
  const handleSearchTextChange = (value: string) => {
    const newParams = {
      ...pathParams,
      searchValue: value,
      page: 1,
    };
    setPathParams(newParams);
    getPosts(newParams);
  };

  // Load posts on page load.
  useEffect(() => {
    let cancelled = false;
    if (!initialLoad) {
      setInitialLoad(true);
      getPosts(pathParams, cancelled);
    }
    return () => {
      cancelled = true;
    };
  }, [getPosts, initialLoad, pathParams]);

  return (
    <>
      { /* @ts-ignore */}
      <TextControl
        value={pathParams.searchValue}
        placeholder={__('Search...', 'alley-scripts')}
        label={__('Search', 'alley-scripts')}
        // @ts-ignore
        onChange={handleSearchTextChange}
      />
      <div className="alley-scripts-post-picker__post-list">
        {listposts ? (
          listposts.map((t) => {
            if (selected && selected.includes(t)) {
              return null;
            }
            return (
              // @ts-ignore
              <Button
                key={t.id}
                className="alley-scripts-post-picker__post"
                onClick={() => setSelected(t)}
              >
                {searchRender ? (
                  searchRender(t)
                ) : (
                  <div>
                    <strong>
                      {t.title}
                    </strong>
                    {sprintf(
                      ' (%s)',
                      t.subtype,
                    )}
                  </div>
                )}
              </Button>
            );
          })
        ) : null}
        {isUpdating ? (
          // @ts-ignore
          <Spinner />
        ) : null}
        {totalPages > 0 && pathParams.page < totalPages ? (
          <div className="alley-scripts-post-picker__load-more">
            {/* @ts-ignore */}
            <Button
              variant="secondary"
              onClick={loadMore}
            >
              {__('Load More', 'alley-scripts')}
            </Button>
          </div>
        ) : null}
      </div>
    </>
  );
}

export default PostList;
