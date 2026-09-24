(function () {
  'use strict';
  function shouldIgnoreLoadingControl(control) {
    if (!control || control.dataset.loadingIgnore === 'true') return true;
    if (control.disabled || control.classList.contains('disabled')) return true;
    if (control.matches('[data-toggle], [data-dismiss], .public-menu-toggle')) return true;
    if (control.tagName === 'A') {
      var href = control.getAttribute('href') || '';
      if (!href || href === '#' || href.indexOf('javascript:') === 0 || href.charAt(0) === '#') return true;
      if (control.target && control.target !== '_self') return true;
    }
    return false;
  }

  function setControlLoading(control, label) {
    if (shouldIgnoreLoadingControl(control)) return;
    var loadingLabel = label || 'Loading...';
    if (control.tagName === 'INPUT') {
      if (!control.dataset.loadingText) control.dataset.loadingText = control.value;
      control.value = loadingLabel;
    } else {
      if (!control.dataset.loadingText) control.dataset.loadingText = control.innerHTML;
      control.innerHTML = '<span class="public-loading-spinner" aria-hidden="true"></span><span>' + loadingLabel + '</span>';
    }
    control.classList.add('is-loading');
    control.setAttribute('aria-busy', 'true');
    if (control.tagName === 'BUTTON' || control.tagName === 'INPUT') control.disabled = true;
  }

  function clearControlLoading(control) {
    if (!control || !control.dataset.loadingText) return;
    control.classList.remove('is-loading');
    control.removeAttribute('aria-busy');
    if (control.tagName === 'INPUT') control.value = control.dataset.loadingText;
    else control.innerHTML = control.dataset.loadingText;
    delete control.dataset.loadingText;
    if (control.tagName === 'BUTTON' || control.tagName === 'INPUT') control.disabled = false;
  }

  function initialiseLoadingStates() {
    document.addEventListener('submit', function (event) {
      var form = event.target;
      if (!form || form.dataset.loadingIgnore === 'true') return;
      var submitter = event.submitter || form.querySelector('button[type="submit"], input[type="submit"]');
      if (submitter) setControlLoading(submitter, submitter.dataset.loadingLabel || 'Loading...');
    }, true);

    document.addEventListener('click', function (event) {
      var control = event.target.closest('a, button');
      if (!control || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
      if (control.closest('form') && control.type === 'submit') return;
      if (control.tagName === 'A') setControlLoading(control, control.dataset.loadingLabel || 'Loading...');
      if (control.dataset.loading === 'true') {
        setControlLoading(control, control.dataset.loadingLabel || 'Loading...');
        window.setTimeout(function () { clearControlLoading(control); }, 12000);
      }
    }, true);
  }

  document.addEventListener('DOMContentLoaded', function () {
    initialiseLoadingStates();
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
      setControlLoading(submit, 'Loading...');
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
        .finally(function () { clearControlLoading(submit); });
    });
  });
}());
