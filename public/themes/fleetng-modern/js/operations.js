(function () {
  'use strict';
  if (!document.body.classList.contains('fleetng-modern')) return;
  var $ = window.jQuery;
  if (!$) return;

  // Retain the existing DataTables contracts and business handlers while modernising controls.
  function enhanceTable(settings) {
    var api = new $.fn.dataTable.Api(settings);
    var wrapper = api.table().container();
    var table = api.table().node();
    if (!table.parentElement.classList.contains('fleetng-table-scroll')) {
      var scroll = document.createElement('div');
      scroll.className = 'fleetng-table-scroll';
      scroll.setAttribute('tabindex','0');
      scroll.setAttribute('aria-label','Scrollable records');
      table.parentNode.insertBefore(scroll,table);
      scroll.appendChild(table);
    }
    table.querySelectorAll('thead th').forEach(function (th) { th.scope = 'col'; });
    if (!wrapper.querySelector('.fleetng-table-error')) {
      var error = document.createElement('div');
      error.className = 'fleetng-table-error';
      error.setAttribute('role', 'alert');
      wrapper.prepend(error);
    }
    var controls = wrapper.querySelector('.fleetng-density-controls');
    if (!controls) {
      controls = document.createElement('div');
      controls.className = 'fleetng-density-controls';
      controls.setAttribute('aria-label', 'Table density');
      controls.innerHTML = '<button type="button" class="btn btn-sm btn-outline-secondary" data-density="cozy" aria-pressed="true">Cozy</button><button type="button" class="btn btn-sm btn-outline-secondary" data-density="compact" aria-pressed="false">Compact</button>';
      var toolbar = wrapper.querySelector('.row');
      if (toolbar) toolbar.appendChild(controls);
      controls.addEventListener('click', function (event) {
        var button = event.target.closest('[data-density]');
        if (!button) return;
        wrapper.classList.toggle('fleetng-density-compact', button.dataset.density === 'compact');
        controls.querySelectorAll('button').forEach(function (b) { b.setAttribute('aria-pressed', String(b === button)); });
      });
    }
    wrapper.querySelectorAll('input[type="search"]').forEach(function (input) { input.setAttribute('aria-label', 'Search ' + (table.getAttribute('aria-label') || 'records')); });
    table.querySelectorAll('a, button').forEach(function (button) {
      if (button.textContent.trim() || button.getAttribute('aria-label')) return;
      var i = button.querySelector('i,svg');
      var classes = i ? (i.getAttribute('class') || '') : '';
      var label = /trash/.test(classes) ? 'Delete record' : /pen|edit/.test(classes) ? 'Edit record' : /unlock|lock/.test(classes) ? 'Change status' : 'Open record';
      button.setAttribute('aria-label', label);
      button.setAttribute('title', label);
    });
  }
  if ($.fn.dataTable) {
    $.fn.dataTable.ext.errMode = 'none';
    $(document).on('init.dt.fleetng draw.dt.fleetng', function (event, settings) { enhanceTable(settings); });
    $(document).on('xhr.dt.fleetng', function (event, settings, json, xhr) {
      var wrapper = new $.fn.dataTable.Api(settings).table().container();
      var error = wrapper.querySelector('.fleetng-table-error');
      if (!error) return;
      error.textContent = json ? '' : xhr && xhr.status === 401 ? 'Your session has expired. Sign in again.' : xhr && xhr.status === 403 ? 'You do not have access to these records.' : 'Records could not be loaded. Refresh to try again.';
    });
    $.fn.dataTable.tables({api:true}).iterator('table', function (settings) { enhanceTable(settings); });
  }
  var summary = document.querySelector('.fleetng-validation-summary');
  if (summary) summary.focus();
  $('.modal').on('shown.bs.modal', function () {
    var control = this.querySelector('input:not([type="hidden"]),select,textarea,button');
    if (control) control.focus();
  });
}());
