(function () {
  'use strict';
  document.addEventListener('DOMContentLoaded', function () {
    var header = document.querySelector('.public-header');
    var toggle = document.querySelector('.public-menu-toggle');
    if (header && toggle) {
      toggle.addEventListener('click', function () {
        header.classList.toggle('menu-open');
        toggle.setAttribute('aria-expanded', String(header.classList.contains('menu-open')));
      });
    }

    var form = document.getElementById('public-contact-form');
    if (!form) return;
    var message = form.querySelector('[data-form-message]');
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      var submit = form.querySelector('[type="submit"]');
      submit.disabled = true;
      message.hidden = true;
      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
        .then(function (response) { return response.json().then(function (data) { return { ok: response.ok, data: data }; }); })
        .then(function (result) {
          message.textContent = result.data.message || (result.ok ? 'Message sent.' : 'Please check the form and try again.');
          message.classList.toggle('error', !result.ok);
          message.hidden = false;
          if (result.ok) form.reset();
        })
        .catch(function () {
          message.textContent = 'The message could not be sent. Please try again.';
          message.classList.add('error');
          message.hidden = false;
        })
        .finally(function () { submit.disabled = false; });
    });
  });
}());
