import { useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';
import {
  Button, SelectControl, TextControl, withNotices,
} from '@wordpress/components';
import ImagePicker from '@/components/imagePicker';
import {
  Checkboxes, Sortable, SortableItem, useOption,
} from '@alleyinteractive/block-editor-tools';
import apiFetch from '@wordpress/api-fetch';
import { v4 as uuidv4 } from 'uuid';

const AdminEmailTypes = withNotices(
  ({ noticeOperations, noticeUI }) => {
    const {
      value: emailTypes = {},
      isEdited = false,
      isSaving = false,
      onChange = () => {},
      onSave = () => {},
    } = useOption('nb_email_types');

    const {
      value: settings = {},
    } = useOption('nb_settings');

    const {
      from_names: fromNames = [],
    } = settings;

    const fromNameOptions = fromNames.map(
      (name) => ({ label: name, value: name }),
    );

    // Get all posts with the nb_template post type.
    const [templatePosts, setTemplatePosts] = useState([]);
    useEffect(() => {
      apiFetch({ path: '/wp/v2/nb_template' })
        .then((response) => {
          const templateOptions = response.map(
            (template) => {
              const {
                id: value = 0,
                title: {
                  rendered: label = '',
                } = {},
              } = template;
              return { label, value };
            },
          );
          setTemplatePosts(templateOptions);
        });
    }, []);

    /**
     * Save settings data and display a notice.
     */
    const { createErrorNotice, createNotice, removeAllNotices } = noticeOperations;
    const saveSettingsData = () => {
      onSave().then(() => {
        // Remove other notices.
        removeAllNotices();

        // Display a success notice.
        createNotice({
          status: 'success',
          content: __('Options updated', 'wp-newsletter-builder'),
        });
      }).catch(() => {
        // Remove other notices.
        removeAllNotices();

        // Display an error notice.
        createErrorNotice(__('Failed to update options', 'wp-newsletter-builder'));
      });
    };

    /**
     * Update settings array with new data.
     * @param {number} index Index to update.
     * @param {string} key Key to update.
     * @param {string} value Value to update.
     */
    const updateSettingsData = (index, key, value) => {
      emailTypes[index][key] = value;
      onChange(emailTypes);
    };

    return (
      <div className="wrap">
        <div className="wp-newsletter-builder-settings__wrapper">
          <h1>{__('Email Types', 'wp-newsletter-builder')}</h1>
          { noticeUI }
          <section className="wp-newsletter-builder-settings__wrapper-group">
            <Sortable
              emptyItem={{
                uuid: uuidv4(), label: '', image: 0, templates: [], from_name: '',
              }}
              list={emailTypes}
            >
              {emailTypes && emailTypes.length
                ? emailTypes.map((emailType, index) => (
                  <div className="wp-newsletter-builder-settings__sortable-item">
                    <SortableItem
                      index={index}
                      key={index} // eslint-disable-line react/no-array-index-key
                      list={emailTypes}
                    >
                      <h2>{__('Email Type', 'wp-newsletter-builder')}</h2>
                      <section className="wp-newsletter-builder-settings__wrapper-group">
                        <TextControl
                          label={__('Label', 'wp-newsletter-builder')}
                          onChange={(value) => updateSettingsData(index, 'label', value)}
                          value={emailType.label}
                        />
                      </section>
                      <section className="wp-newsletter-builder-settings__wrapper-group">
                        <ImagePicker
                          label={__('Image', 'wp-newsletter-builder')}
                          onChange={(value) => updateSettingsData(index, 'image', value)}
                          value={emailType.image}
                        />
                      </section>
                      <section className="wp-newsletter-builder-settings__wrapper-group">
                        {templatePosts.length !== 0 ? (
                          <Checkboxes
                            label={__('Templates', 'wp-newsletter-builder')}
                            onChange={(value) => updateSettingsData(index, 'templates', value)}
                            options={templatePosts}
                            value={emailType.templates}
                          />
                        ) : <p>{__('No templates found.', 'wp-newsletter-builder')}</p>}
                      </section>
                      <section className="wp-newsletter-builder-settings__wrapper-group">
                        {fromNameOptions.length !== 0 ? (
                          <SelectControl
                            label={__('From Name', 'wp-newsletter-builder')}
                            onChange={(value) => updateSettingsData(index, 'from_name', value)}
                            options={fromNameOptions}
                            value={emailType.from_name}
                          />
                        ) : <p>{__('No from names found.', 'wp-newsletter-builder')}</p>}
                      </section>
                    </SortableItem>
                  </div>
                ))
                : null}
            </Sortable>
          </section>
          <Button
            variant="primary"
            onClick={saveSettingsData}
            disabled={!isEdited}
            isBusy={isSaving}
          >
            {__('Save Changes', 'wp-newsletter-builder')}
          </Button>
        </div>
      </div>
    );
  },
);

export default AdminEmailTypes;
