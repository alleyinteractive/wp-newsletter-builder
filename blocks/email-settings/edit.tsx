/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { TextControl, Spinner } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import { dispatch } from '@wordpress/data';
import { parse } from '@wordpress/blocks';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

// import { usePostMeta } from '@alleyinteractive/block-editor-tools';

import { MultiSelect } from 'react-multi-select-component';

import EmailTypeSelector from '../../components/emailTypeSelector';

import usePostMeta from '../../hooks/usePostMeta';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './index.scss';

interface ListResult {
  ListID: string;
  Name: string;
}

interface Option {
  value: string;
  label: string;
}

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit() {
  const [meta, setMeta] = usePostMeta();
  const {
    nb_newsletter_subject: subject,
    nb_newsletter_preview: preview,
    nb_newsletter_list: list,
    nb_newsletter_email_type: emailtype,
    nb_newsletter_template: template,
    nb_newsletter_from_name: fromname,
  } = meta;

  const typeHandler = (newValue: string) => {
    setMeta({ nb_newsletter_email_type: newValue });
  };

  const templateHandler = (newValue: string) => {
    setMeta({ nb_newsletter_template: newValue });
  };

  const fromNameHandler = (newValue: string) => {
    setMeta({ nb_newsletter_from_name: newValue });
  };

  const imageHandler = (image: number) => {
    setMeta({ nb_newsletter_header_img: image });
  };

  const contentHandler = (content: string) => {
    dispatch('core/block-editor').resetBlocks(parse(content));
  };

  const [lists, setLists] = useState<ListResult[]>([]);
  const listArray = Array.isArray(list) ? list : [list];

  const setSelectedLists = ((newValue: Array<Option>) => {
    const listIds = newValue.map((item: Option) => item.value);
    setMeta({ nb_newsletter_list: listIds });
  });

  const listsToOptions = (rawLists: ListResult[]) => {
    const output = rawLists.map((item: ListResult) => ({ label: item.Name, value: item.ListID }));
    return output;
  };

  const options = lists.length > 0 ? listsToOptions(lists) : [];
  const selected = options.filter((item: Option) => listArray.includes(item.value));

  useEffect(() => {
    if (lists.length > 0) {
      return;
    }
    apiFetch({ path: '/wp-newsletter-builder/v1/lists' }).then((response) => {
      setLists(response as any as ListResult[]);
    });
  }, [lists]);

  return (
    <div {...useBlockProps()}>
      <EmailTypeSelector
        typeValue={emailtype}
        contentHandler={contentHandler}
        typeHandler={typeHandler}
        imageHandler={imageHandler}
        templateHandler={templateHandler}
        fromNameHandler={fromNameHandler}
        templateValue={template}
        fromNameValue={fromname}
      />
      {/* @ts-ignore */}
      <TextControl
        label={__('Subject', 'wp-newsletter-builder')}
        placeholder={__('Enter subject', 'wp-newsletter-builder')}
        value={subject}
        onChange={(newValue: string) => setMeta({ nb_newsletter_subject: newValue })}
      />
      {/* @ts-ignore */}
      <TextControl
        label={__('Preview Text', 'wp-newsletter-builder')}
        placeholder={__('Enter preview text', 'wp-newsletter-builder')}
        value={preview}
        onChange={(newValue: string) => setMeta({ nb_newsletter_preview: newValue })}
      />
      {/* @ts-ignore */}
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
        /* @ts-ignore */
        <Spinner />
      )}
    </div>
  );
}
