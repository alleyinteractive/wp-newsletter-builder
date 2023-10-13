window.addEventListener('DOMContentLoaded', () => {
  const forms = document.querySelectorAll('[data-component="newsletter-builder-signup"]');

  forms.forEach((form) => {
    const button = form.querySelector('button');

    if (button) {
      button.addEventListener('click', (event) => {
        event.preventDefault();
        const email = form.querySelector('input[type="email"]') as HTMLInputElement;
        const listIds = form.querySelectorAll('input[type="checkbox"]:checked');
        const listId = form.querySelector('input[name="newsletter-builder-hidden"]') as HTMLInputElement;
        const responseDiv = form.querySelector('.wp-block-newsletter-builder-signup-form__response');
        if (!responseDiv || !email) {
          return;
        }
        responseDiv.innerHTML = '';
        responseDiv.classList.remove('success', 'error');

        if (!email.value) {
          responseDiv.classList.add('error');
          responseDiv.innerHTML = 'Email is required';
          email.focus();
          return;
        }
        if (!listIds.length && !listId) {
          responseDiv.classList.add('error');
          responseDiv.innerHTML = 'Please select a newsletter';
          return;
        }

        const formData = new URLSearchParams();
        formData.append('email', email.value);
        if (listId) {
          formData.append('listIds', listId.value);
        } else {
          formData.append('listIds', (Array.from(listIds).map((list) => (list as HTMLInputElement).value) ?? []).join(','));
        }

        fetch('/wp-json/newsletter-builder/v1/subscribe', {
          method: 'POST',
          body: formData,
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
        }).then((response) => (
          response.json()
        )).then((data) => {
          const { success, message } = data;
          responseDiv.classList.add(success ? 'success' : 'error');
          responseDiv.innerHTML = message;
        });
      });
    }
  });
});
