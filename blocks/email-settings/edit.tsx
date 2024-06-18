import { MultiSelect } from 'react-multi-select-component';
import { __ } from '@wordpress/i18n';
import { TextControl, Spinner, SelectControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import { dispatch, useSelect } from '@wordpress/data';
import { BlockInstance, parse } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';

import EmailTypeSelector from '../../components/emailTypeSelector';

import usePostMeta from '../../hooks/usePostMeta';

import './index.scss';

interface ListResult {
  ListID: string;
  Name: string;
}

interface Option {
  value: string;
  label: string;
}

interface Window {
  newsletterBuilder: {
    usesSuppressionLists: boolean;
  };
}

export default function Edit() {
  const [meta, setMeta] = usePostMeta();
  const {
    nb_newsletter_subject: subject,
    nb_newsletter_preview: preview,
    nb_newsletter_list: list,
    nb_newsletter_email_type: emailtype,
    nb_newsletter_template: template,
    nb_newsletter_from_name: fromname,
    nb_newsletter_suppression_group: suppressionGroup,
  } = meta;

  const {
    newsletterBuilder: {
      usesSuppressionLists = false,
    } = {},
  } = (window as any as Window);

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

  const [suppressionLists, setSuppressionLists] = useState<ListResult[]>([]);

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

  useEffect(() => {
    if (suppressionLists.length > 0) {
      return;
    }
    apiFetch({ path: '/wp-newsletter-builder/v1/suppression-lists' }).then((response) => {
      const newLists = response as any as ListResult[];
      newLists.unshift({ Name: __('Select a suppression list', 'wp-newsletter-builder'), ListID: '' });

      setSuppressionLists(newLists);
    });
  }, [suppressionLists]);

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
      {/* @ts-ignore */}
      {usesSuppressionLists && suppressionLists.length > 0 ? (
        <SelectControl
          label={__('Suppression Group', 'wp-newsletter-builder')}
          value={suppressionGroup}
          options={suppressionLists.map((item) => ({ label: item.Name, value: item.ListID }))}
          onChange={(newValue: string) => setMeta({ nb_newsletter_suppression_group: newValue })}
        />
      ) : (
        /* @ts-ignore */
        null
      )}
    </div>
  );
}
