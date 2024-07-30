import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';

// Components.
import AdminGeneralSettings from './index';

const element = document.getElementById('newsletter-builder-options__page');

if (element) {
  const root = createRoot(element);

  if (root) {
    root.render(
      <StrictMode>
        <AdminGeneralSettings />
      </StrictMode>,
    );
  }
}
