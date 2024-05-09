import { Spinner } from '@wordpress/components';

/**
 * Wrapper for the spinner component.
 *
 * @see https://github.com/WordPress/gutenberg/issues/61322
 */
export default function NewsletterSpinner() {
  // @ts-ignore
  return <Spinner />;
}
