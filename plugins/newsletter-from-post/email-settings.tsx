/**
 * EmailSettings component
 */

import { PluginSidebar } from '@wordpress/edit-post';
import {
  PanelBody,
  TextareaControl,
  CheckboxControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useMemo, useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { usePostMeta } from '@alleyinteractive/block-editor-tools';
import { MultiSelect } from 'react-multi-select-component';
import { parse, serialize } from '@wordpress/blocks';

import NewsletterSpinner from '@/components/newsletterSpinner';
import EmailTypeSelector from '../../components/emailTypeSelector';
import SentNewsletter from './sent-newsletter';

interface ListResult {
  ListID: string;
  Name: string;
}

interface Option {
  value: string;
  label: string;
}

interface CoreEditor {
  getEditedPostAttribute: (attribute: string) => string;
}

interface Window {
  newsletterBuilder: {
    breakingLists: {
      [key: string]: string;
    }
  };
}

function EmailSettings() {
  const [meta, setMeta] = usePostMeta();
  const [lists, setLists] = useState<ListResult[]>([]); // eslint-disable-line

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

  const {
    postId,
    postStatus,
    postTitle,
    postExcerpt,
  } = useSelect((select) => {
    const { getEditedPostAttribute } = select('core/editor') as CoreEditor;
    let tempPostExcerpt = getEditedPostAttribute('excerpt');
    if (tempPostExcerpt === '') {
      const tempcontent = getEditedPostAttribute('content');
      const matches = tempcontent ? tempcontent.match(/<p>(.*?)<\/p>/) : [];
      tempPostExcerpt = matches && matches[1] ? matches[1] : '';
    }

    return {
      postId: getEditedPostAttribute('id'),
      postStatus: getEditedPostAttribute('status'),
      postTitle: getEditedPostAttribute('title'),
      postExcerpt: tempPostExcerpt,
    };
  }, []);

  const options = useMemo(() => {
    if (lists.length > 0) {
      return [];
    }

    return lists
      .map((item: ListResult) => ({ label: item.Name, value: item.ListID }));
  }, [lists]);

  const manualSubject = subject !== '';
  const manualPreview = preview !== '';

  const listArray = Array.isArray(list) ? list : [list];

  const {
    newsletterBuilder: {
      breakingLists = {},
    },
  } = (window as any as Window);

  const setSelectedLists = ((newValue: Array<Option>) => {
    const listIds = newValue.map((item: Option) => item.value);
    setMeta({ nb_breaking_list: listIds });
  });

  const selected = options.filter((item: Option) => listArray.includes(item.value));

  useEffect(() => { // eslint-disable-line
    if (lists.length > 0) {
      return;
    }
    apiFetch({ path: '/wp-newsletter-builder/v1/lists' }).then((response) => {
      setLists(response as any as ListResult[]);
    });
  }, [lists]);

  const contentHandler = (html: string) => {
    const blocks = parse(html);
    const postIndex = blocks.findIndex((block) => block.name === 'wp-newsletter-builder/post');
    blocks[postIndex].attributes.postId = postId;

    setMeta({ nb_breaking_content: serialize(blocks) });
  };

  const disabled = type === ''
    || template === ''
    || fromName === ''
    || (subject === '' && postTitle === '')
    || (preview === '' && postExcerpt === '')
    || list.length === 0;
  return (
    <PluginSidebar
      icon="email-alt2"
      name="nb-newsletter"
      title={__('Newsletter', 'wp-newsletter-builder')}
    >
      <PanelBody
        initialOpen
        title={__('Send Newsletter', 'wp-newsletter-builder')}
      >
        <EmailTypeSelector
          contentHandler={contentHandler}
          typeHandler={(newType) => { setMeta({ nb_breaking_email_type: newType }); }}
          imageHandler={(newImage) => { setMeta({ nb_breaking_header_img: newImage }); }}
          typeValue={type}
          templateHandler={(newTemplate) => { setMeta({ nb_breaking_template: newTemplate }); }}
          fromNameHandler={(newFromName) => { setMeta({ nb_breaking_from_name: newFromName }); }}
          templateValue={template}
          fromNameValue={fromName}
        />
        <TextareaControl
          label={manualSubject ? __('Subject', 'wp-newsletter-builder') : __('Subject (linked)', 'wp-newsletter-builder')}
          placeholder={__('Enter subject', 'wp-newsletter-builder')}
          value={subject !== '' ? subject : postTitle}
          onChange={(value) => { setMeta({ nb_breaking_subject: value }); }}
        />
        <TextareaControl
          label={manualPreview ? __('Preview Text', 'wp-newsletter-builder') : __('Preview Text (linked)', 'wp-newsletter-builder')}
          placeholder={__('Enter preview text', 'wp-newsletter-builder')}
          value={preview !== '' ? preview : postExcerpt}
          onChange={(value) => { setMeta({ nb_breaking_preview: value }); }}
        />
        {Object.keys(breakingLists).map((key: string) => {
          const value: string = breakingLists[key];
          return (
            <CheckboxControl
              label={value}
              checked={listArray.includes(key)}
              onChange={(checked) => {
                const newList = checked
                  ? [...listArray, key]
                  : listArray.filter((item) => item !== key);
                setMeta({ nb_breaking_list: newList });
              }}
            />
          );
        })}
        {lists.length > 0 ? (
          <label
            htmlFor="wp-newsletter-builder-list"
          >
            {__('Email List', 'wp-newsletter-builder')}
            <MultiSelect
              labelledBy={__('List', 'wp-newsletter-builder')}
              value={selected}
              options={options}
              onChange={setSelectedLists}
              hasSelectAll={false}
              overrideStrings={{
                selectSomeItems: __('Select Email List', 'wp-newsletter-builder'),
              }}
            />
          </label>
        ) : (
          <NewsletterSpinner />
        )}
        <div style={{ marginTop: '1rem' }}>
          <CheckboxControl
            label={postStatus === 'draft' || postStatus === 'auto-draft' ? __('Send Newsletter on Publish', 'wp-newsletter-builder') : __('Send Newsletter on Update', 'wp-newsletter-builder')}
            checked={send && !disabled}
            onChange={(value) => { setMeta({ nb_breaking_should_send: value }); }}
            disabled={disabled}
          />
          {!type ? (
            <p style={{ color: 'red' }}>{__('Header Type is Required', 'wp-newsletter-builder')}</p>
          ) : null}
          {!template ? (
            <p style={{ color: 'red' }}>{__('Template is Required', 'wp-newsletter-builder')}</p>
          ) : null}
          {!fromName ? (
            <p style={{ color: 'red' }}>{__('From Name is Required', 'wp-newsletter-builder')}</p>
          ) : null}
          {!subject && !postTitle ? (
            <p style={{ color: 'red' }}>{__('Subject is Required', 'wp-newsletter-builder')}</p>
          ) : null}
          {!preview && !postExcerpt ? (
            <p style={{ color: 'red' }}>{__('Preview Text is Required', 'wp-newsletter-builder')}</p>
          ) : null}
          {list.length === 0 ? (
            <p style={{ color: 'red' }}>{__('Email List is Required', 'wp-newsletter-builder')}</p>
          ) : null}
        </div>
      </PanelBody>
      {sentBreakingPostId ? (
        <PanelBody
          initialOpen={false}
          title={__('Sent Newsletters', 'wp-newsletter-builder')}
        >
          {sentBreakingPostId.map((id: number) => (
            <SentNewsletter postId={id} key={id} />
          ))}
        </PanelBody>
      ) : null}
    </PluginSidebar>
  );
}

export default EmailSettings;
