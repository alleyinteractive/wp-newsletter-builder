interface Window {
  newsletterBuilder?: {
    // wp_localize_script() passes booleans as strings: '1' or ''.
    sendsEmail?: boolean | string;
  };
}

/**
 * Whether the selected email provider sends email. When it doesn't, there are
 * no email lists or suppression lists to choose from or require.
 *
 * @returns {boolean}
 */
const sendsEmail = (): boolean => {
  const value = (globalThis as any as Window).newsletterBuilder?.sendsEmail;
  return value === undefined || Boolean(value);
};

export default sendsEmail;
