import sendsEmail from './index';

describe('sendsEmail', () => {
  afterEach(() => {
    delete (globalThis as any).newsletterBuilder;
  });

  it('defaults to true when no settings are localized', () => {
    expect(sendsEmail()).toBe(true);
  });

  it('defaults to true when the setting is missing', () => {
    (globalThis as any).newsletterBuilder = {};
    expect(sendsEmail()).toBe(true);
  });

  it.each([false, ''])('is false when the provider does not send email (%p)', (value) => {
    (globalThis as any).newsletterBuilder = { sendsEmail: value };
    expect(sendsEmail()).toBe(false);
  });

  it.each([true, '1'])('is true when the provider sends email (%p)', (value) => {
    (globalThis as any).newsletterBuilder = { sendsEmail: value };
    expect(sendsEmail()).toBe(true);
  });
});
