import { usePostMetaValue } from '@alleyinteractive/block-editor-tools';
import { MultiSelect } from 'react-multi-select-component';
import { __ } from '@wordpress/i18n';
import { TextControl, Spinner, SelectControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from 'react';
import { dispatch, useSelect } from '@wordpress/data';
import { BlockInstance, parse } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';

import EmailTypeSelector from '../../components/emailTypeSelector';

import './index.scss';

interface BlockEditor {
  getBlocksByName: (attribute: string) => string[];
  getBlocksByClientId: (attribute: string) => BlockInstance[];
}

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
  const [emailType, setEmailType] = usePostMetaValue('nb_newsletter_email_type');
  const [fromName, setFromName] = usePostMetaValue('nb_newsletter_from_name');
  const [, setHeaderImg] = usePostMetaValue('nb_newsletter_header_img');
  const [list, setList] = usePostMetaValue('nb_newsletter_list');
  const [preview, setPreview] = usePostMetaValue('nb_newsletter_preview');
  const [subject, setSubject] = usePostMetaValue('nb_newsletter_subject');
  const [suppressionGroup, setSuppressionGroup] = usePostMetaValue('nb_newsletter_suppression_group');
  const [template, setTemplate] = usePostMetaValue('nb_newsletter_template');

  const contentController = useSelect((select) => {
    const { getBlocksByName, getBlocksByClientId } = select('core/block-editor') as BlockEditor;
    return {
      getEmailSettingsBlocks: () => getBlocksByName('wp-newsletter-builder/email-settings'),
      getBlocksByClientId,
    };
  }, []);

  const {
    newsletterBuilder: {
      usesSuppressionLists = false,
    } = {},
  } = (window as any as Window);

  const contentHandler = (content: string) => {
    const emailSettingsBlockIds = contentController.getEmailSettingsBlocks();
    const emailSettingsBlockInstances = contentController
      .getBlocksByClientId(emailSettingsBlockIds[0]);

    const parsedContentFromTemplate = parse(content);

    const emailSettingsBlockAlreadyPresent = parsedContentFromTemplate
      .filter((parsedBlock) => parsedBlock.name === 'wp-newsletter-builder/email-settings');

    let newBlocks: BlockInstance[] = [];
    if (emailSettingsBlockAlreadyPresent.length === 0) {
      newBlocks = [...emailSettingsBlockInstances, ...parsedContentFromTemplate];
    } else {
      newBlocks = [...parsedContentFromTemplate];
    }

    // @ts-expect-error
    dispatch('core/block-editor').resetBlocks(newBlocks);
  };

  const [lists, setLists] = useState<ListResult[]>([]);
  const listArray = Array.isArray(list) ? list : [list];

  const [suppressionLists, setSuppressionLists] = useState<ListResult[]>([]);

  const setSelectedLists = (newValue: Array<Option>) => setList(
    newValue.map((item: Option) => item.value),
  );

  const listsToOptions = (rawLists: ListResult[]) => rawLists
    .map((item: ListResult) => ({ label: item.Name, value: item.ListID }));

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
        typeValue={emailType}
        contentHandler={contentHandler}
        typeHandler={setEmailType}
        imageHandler={setHeaderImg}
        templateHandler={setTemplate}
        fromNameHandler={setFromName}
        templateValue={template}
        fromNameValue={fromName}
      />
      {/* @ts-ignore */}
      <TextControl
        label={__('Subject', 'wp-newsletter-builder')}
        placeholder={__('Enter subject', 'wp-newsletter-builder')}
        value={subject}
        onChange={setSubject}
      />
      {/* @ts-ignore */}
      <TextControl
        label={__('Preview Text', 'wp-newsletter-builder')}
        placeholder={__('Enter preview text', 'wp-newsletter-builder')}
        value={preview}
        onChange={setPreview}
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
          onChange={setSuppressionGroup}
          required
        />
      ) : null}
    </div>
  );
}
