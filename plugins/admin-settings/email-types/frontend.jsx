import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';

// Components.
import AdminEmailTypes from './index';

const element = document.getElementById('wp-newsletter-builder-settings__page-email-types');

if (element) {
  const root = createRoot(element);

  if (root) {
    root.render(
      <StrictMode>
        <AdminEmailTypes />
      </StrictMode>,
    );
  }
}
