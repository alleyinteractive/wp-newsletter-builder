/**
 * EmailSettings component
 */

import { PluginSidebar } from '@wordpress/edit-post';
import { CheckboxControl, PanelBody, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { MultiSelect } from 'react-multi-select-component';
import { createBlock, parse, serialize } from '@wordpress/blocks';
import NewsletterSpinner from '@/components/newsletterSpinner';
import useNewsletterMeta from '@/hooks/useNewsletterMeta';
import useEmailLists, { Option } from '@/hooks/useEmailLists';
import RequiredFields from '@/plugins/newsletter-from-post/components/required-fields';

import EmailTypeSelector from '../../components/emailTypeSelector';
import SentNewsletter from './components/sent-newsletter';

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
  const { meta, setMeta } = useNewsletterMeta();
  const { emailListOptions, selectedEmailList } = useEmailLists();

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

  const manualSubject = meta.subject !== '';
  const manualPreview = meta.preview !== '';

  const listArray = Array.isArray(meta.list) ? meta.list : [meta.list];

  const {
    newsletterBuilder: {
      breakingLists = {},
    },
  } = (window as any as Window);

  const setSelectedLists = ((newValue: Array<Option>) => {
    const listIds = newValue.map((item: Option) => item.value);
    setMeta({ nb_breaking_list: listIds });
  });

  const contentHandler = (html: string) => {
    const blocks = parse(html);
    const postIndex = blocks.findIndex((block) => block.name === 'wp-newsletter-builder/post');

    blocks[postIndex] = createBlock('wp-newsletter-builder/post', {
      ...blocks[postIndex].attributes,
      postId,
    }, blocks[postIndex].innerBlocks);

    setMeta({ nb_breaking_content: serialize(blocks) });
  };

  const disabled = meta.type === ''
    || meta.template === ''
    || meta.fromName === ''
    || (meta.subject === '' && postTitle === '')
    || (meta.preview === '' && postExcerpt === '')
    || meta.list.length === 0;

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
          typeValue={meta.type}
          templateHandler={(newTemplate) => { setMeta({ nb_breaking_template: newTemplate }); }}
          fromNameHandler={(newFromName) => { setMeta({ nb_breaking_from_name: newFromName }); }}
          templateValue={meta.template}
          fromNameValue={meta.fromName}
        />
        <TextareaControl
          label={manualSubject ? __('Subject', 'wp-newsletter-builder') : __('Subject (linked)', 'wp-newsletter-builder')}
          placeholder={__('Enter subject', 'wp-newsletter-builder')}
          value={meta.subject !== '' ? meta.subject : postTitle}
          onChange={(value) => { setMeta({ nb_breaking_subject: value }); }}
        />
        <TextareaControl
          label={manualPreview ? __('Preview Text', 'wp-newsletter-builder') : __('Preview Text (linked)', 'wp-newsletter-builder')}
          placeholder={__('Enter preview text', 'wp-newsletter-builder')}
          value={meta.preview !== '' ? meta.preview : postExcerpt}
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
        {emailListOptions.length > 0 ? (
          <label
            htmlFor="wp-newsletter-builder-list"
          >
            {__('Email List', 'wp-newsletter-builder')}
            <MultiSelect
              labelledBy={__('List', 'wp-newsletter-builder')}
              value={selectedEmailList}
              options={emailListOptions}
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
            checked={meta.send && !disabled}
            onChange={(value) => { setMeta({ nb_breaking_should_send: value }); }}
            disabled={disabled}
          />
          <RequiredFields meta={meta} postTitle={postTitle} postExcerpt={postExcerpt} />
        </div>
      </PanelBody>
      {meta.sentBreakingPostId ? (
        <PanelBody
          initialOpen={false}
          title={__('Sent Newsletters', 'wp-newsletter-builder')}
        >
          {meta.sentBreakingPostId.map((id: number) => (
            <SentNewsletter postId={id} key={id} />
          ))}
        </PanelBody>
      ) : null}
    </PluginSidebar>
  );
}

export default EmailSettings;
