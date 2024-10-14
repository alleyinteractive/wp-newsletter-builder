import { __ } from '@wordpress/i18n';
import { Button, TextControl, withNotices } from '@wordpress/components';
import ImagePicker from '@/components/imagePicker';
import { Sortable, SortableItem, useOption } from '@alleyinteractive/block-editor-tools';

const AdminGeneralSettings = withNotices(
  ({ noticeOperations, noticeUI }) => {
    const {
      value: settings = {},
      isEdited = false,
      isSaving = false,
      onChange = () => {},
      onSave = () => {},
    } = useOption('nb_settings');

    const {
      from_email: fromEmail = '',
      reply_to_email: replyToEmail = '',
      from_names: fromNames = [],
      facebook_url: facebookUrl = '',
      twitter_url: twitterUrl = '',
      instagram_url: instagramUrl = '',
      youtube_url: youtubeUrl = '',
      image = 0,
      address = '',
    } = settings;

    /**
     * Update settings array with new data.
     * @param {string} key Key to update.
     * @param {string} value Value to update.
     */
    const updateSettingsData = (key, value) => {
      onChange({
        ...settings,
        [key]: value,
      });
    };

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

    return (
      <div className="wrap">
        <div className="wp-newsletter-builder-settings__wrapper">
          <h1>{__('General Settings', 'wp-newsletter-builder')}</h1>
          { noticeUI }
          <section className="wp-newsletter-builder-settings__wrapper-group">
            <h2>{__('From Email', 'wp-newsletter-builder')}</h2>
            <TextControl
              hideLabelFromVision
              label={__('From Email', 'wp-newsletter-builder')}
              onChange={(value) => updateSettingsData('from_email', value)}
              value={fromEmail}
            />
          </section>
          <section className="wp-newsletter-builder-settings__wrapper-group">
            <h2>{__('Reply-To Email', 'wp-newsletter-builder')}</h2>
            <TextControl
              hideLabelFromVision
              label={__('Reply To Email', 'wp-newsletter-builder')}
              onChange={(value) => updateSettingsData('reply_to_email', value)}
              value={replyToEmail}
            />
          </section>
          <section className="wp-newsletter-builder-settings__wrapper-group">
            <h2>{__('From Names', 'wp-newsletter-builder')}</h2>
            <Sortable
              emptyItem=""
              list={fromNames}
              setList={(value) => updateSettingsData('from_names', value)}
            >
              {fromNames && fromNames.length
                ? fromNames.map((fromName, index) => (
                  <SortableItem
                    index={index}
                    key={index} // eslint-disable-line react/no-array-index-key
                    list={fromNames}
                    setList={(value) => updateSettingsData('from_names', value)}
                  >
                    <TextControl
                      label={__('From Name', 'wp-newsletter-builder')}
                      onChange={(value) => {
                        const updatedFromNames = [...fromNames];
                        updatedFromNames[index] = value;
                        updateSettingsData('from_names', updatedFromNames);
                      }}
                      value={fromName}
                    />
                  </SortableItem>
                )) : null}
            </Sortable>
          </section>
          <section className="wp-newsletter-builder-settings__wrapper-group">
            <h2>{__('Footer Settings', 'wp-newsletter-builder')}</h2>
            <TextControl
              label={__('Facebook URL', 'wp-newsletter-builder')}
              onChange={(value) => updateSettingsData('facebook_url', value)}
              value={facebookUrl}
            />
            <TextControl
              label={__('Twitter URL', 'wp-newsletter-builder')}
              onChange={(value) => updateSettingsData('twitter_url', value)}
              value={twitterUrl}
            />
            <TextControl
              label={__('Instagram URL', 'wp-newsletter-builder')}
              onChange={(value) => updateSettingsData('instagram_url', value)}
              value={instagramUrl}
            />
            <TextControl
              label={__('YouTube URL', 'wp-newsletter-builder')}
              onChange={(value) => updateSettingsData('youtube_url', value)}
              value={youtubeUrl}
            />
            <ImagePicker
              label={__('Image', 'wp-newsletter-builder')}
              onChange={(value) => updateSettingsData('image', value)}
              value={image}
            />
            <TextControl
              label={__('Company Address', 'wp-newsletter-builder')}
              onChange={(value) => updateSettingsData('address', value)}
              value={address}
            />
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

export default AdminGeneralSettings;
