import { SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

interface AdTag {
  tag_code: string;
}

interface TypeResult {
  [key: string]: {
    label: string;
    image: string;
    templates: Array<string>;
    ad_tags: AdTag[];
    from_name: string;
  }
}

interface EmailTypeSelectorProps {
  contentHandler: (newValue: string) => void;
  typeHandler: (newValue: string) => void;
  imageHandler: (newValue: number) => void;
  templateHandler: (newValue: string) => void;
  fromNameHandler: (newValue: string) => void;
  typeValue: string;
  templateValue: string;
  fromNameValue: string;
}

interface Window {
  newsletterBuilder: {
    fromNames: Array<string>;
  };
}

const {
  newsletterBuilder: {
    fromNames = [],
  } = {},
} = (window as any as Window);

const templatesMap = {
  'single-story.html': __('Single Story', 'wp-newsletter-builder'),
  'multi-story.html': __('Multi Story', 'wp-newsletter-builder'),
  'pro-single-story.html': __('Pro Single Story', 'wp-newsletter-builder'),
  'pro-multi-story.html': __('Pro Multi Story', 'wp-newsletter-builder'),
  'first-take.html': __('First Take', 'wp-newsletter-builder'),
};

const fromOptions = fromNames.map((name: string) => (
  { value: name, label: name }
));

function EmailTypeSelector({
  contentHandler,
  typeHandler,
  imageHandler,
  templateHandler,
  fromNameHandler,
  typeValue,
  templateValue,
  fromNameValue,
}: EmailTypeSelectorProps) {
  const [types, setTypes] = useState<TypeResult>({} as TypeResult);

  useEffect(() => {
    if (Object.keys(types).length > 0) {
      return;
    }
    apiFetch({ path: '/wp-newsletter-builder/v1/email-types' }).then((response) => {
      setTypes(response as any as TypeResult);
    });
  }, [types]);

  // Set the meta to be the first option if it's not set. This matches what is already
  // happpening in the UI.
  useEffect(() => {
    if (!fromNameValue) {
      fromNameHandler(fromNames[0]);
    }
  }, [fromNameHandler, fromNameValue]);

  const typesToOptions = (rawTypes: TypeResult) => {
    const output = Object.keys(rawTypes).map((key: string) => (
      { label: rawTypes[key].label, value: key }
    ));
    output.unshift({ label: __('Select a type', 'wp-newsletter-builder'), value: '' });
    return output;
  };

  const templateToOptions = (rawTypes: TypeResult) => {
    const templates = rawTypes[typeValue]?.templates ?? [];
    if (!templates.length) {
      return [];
    }
    const output = templates.map((value) => (
      { value, label: templatesMap[value as keyof typeof templatesMap] }
    ));
    output.unshift({ label: __('Select a template', 'wp-newsletter-builder'), value: '' });
    return output;
  };

  const handleChange = async (value: string) => {
    templateHandler(value);
    if (!value) {
      return;
    }
    const type = types[typeValue];
    const { image, from_name: fromName } = type;
    imageHandler(parseInt(image, 10));
    fromNameHandler(fromName);
    const response = await fetch(`/wp-content/plugins/wp-newsletter-builder/layouts/${value}`);
    const html = await response.text();
    contentHandler(html);
  };

  // Set the template to be the first option if there's only one option.
  useEffect(() => {
    if (!typeValue) {
      return;
    }
    const templates = types[typeValue]?.templates;
    if (templates && templates.length === 1) {
      handleChange(templates[0]);
    }
  }, [typeValue]); // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <>
      <SelectControl
        label={__('Select Header Type', 'wp-newsletter-builder')}
        value={typeValue}
        options={typesToOptions(types)}
        onChange={typeHandler}
      />
      { templateToOptions(types).length ? (
        <SelectControl
          label={__('Select Template', 'wp-newsletter-builder')}
          value={templateValue}
          options={templateToOptions(types)}
          onChange={handleChange}
        />
      ) : null}
      <SelectControl
        label={__('From Name', 'wp-newsletter-builder')}
        value={fromNameValue || types[typeValue]?.from_name}
        options={fromOptions}
        onChange={fromNameHandler}
      />
    </>
  );
}

export default EmailTypeSelector;
