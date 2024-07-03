# Changelog

All notable changes to `Newsletter Builder` will be documented in this file.

## 0.4.0 - 2024-07-03

- Only load Newsletter Builder blocks on Newsletter Builder post types (nb_newsletter and nb_template)

## 0.3.11 - 2024-05-09

- Adds a `NewsletterSpinner` wrapper component for the `<Spinner />` component from `@wordpress/components`
  - [see link to GitHub issue](https://github.com/WordPress/gutenberg/issues/61322)
- Change `nb_newsletter_template` post meta from type `string` to type `number`
- Change SendGrid Provider to send `suppression_group_id` and remove sending `custom_unsubscribe_url`

## 0.3.10 - 2024-05-06

- Removes instances of `@phpstan-ignore-next-line the Fieldmanager doc block is incorrect` which are unnecessary now that WordPress Fieldmanager doc blocks have been updated

## 0.3.9 - 2024-05-06

- Adds `wp_newsletter_builder_allowed_post_types` filter for filtering post types that appear in the post picker

## 0.3.8 - 2024-04-29

- Update dependencies, bump PHP version to 8.1

## 0.3.7 - 2024-04-01

- Adds phpstan package and fixes phpstan issues
- Adds code-quality GitHub workflow

## 0.3.6 - 2024-03-26

- Adds placeholder in the image header block

## 0.3.5 - 2023-11-20

- Update dependencies, minor bugfixes

## 0.3.4 - 2023-11-13

- Allow editing of post block attributes with no block selected. Allow order attribute to be edited.

## 0.3.3 - 2023-11-10

- Add URL override attribute and slotfill to post block

## 0.3.1 - 2023-11-08

- Remove singleton pattern of Email Providers

## 0.3.0 - 2023-11-07

- Set up email provider adapter system

## 0.2.0 - 2023-11-07

- Move templates from static files to using a custom post type

## 0.1.0 - 2023-10-13

- Initial release
