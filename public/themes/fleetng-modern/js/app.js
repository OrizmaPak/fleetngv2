(function () {
  'use strict';

  var root = document.documentElement;
  var body = document.body;
  var storage = window.localStorage;

  function refreshIcons() {
    if (window.feather) window.feather.replace({ width: 16, height: 16 });
  }

  function setColourTheme(theme) {
    var next = theme === 'dark' ? 'dark' : 'light';
    root.setAttribute('data-fleetng-theme', next);
    storage.setItem('fleetng-colour-theme', next);
    var meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.setAttribute('content', next === 'dark' ? '#050b18' : '#377df6');
  }

  function initialiseNavigation() {
    var collapse = document.querySelector('.fleetng-nav-collapse');
    var mobileToggle = document.querySelector('.fleetng-mobile-toggle');
    var overlay = document.querySelector('.sidenav-overlay');
    var collapsed = storage.getItem('fleetng-nav-collapsed') === 'true';
    if (collapsed && window.innerWidth >= 1200) body.classList.add('fleetng-nav-collapsed');

    if (collapse) {
      collapse.addEventListener('click', function () {
        body.classList.toggle('fleetng-nav-collapsed');
        var isCollapsed = body.classList.contains('fleetng-nav-collapsed');
        collapse.setAttribute('aria-expanded', String(!isCollapsed));
        storage.setItem('fleetng-nav-collapsed', String(isCollapsed));
      });
    }

    function closeMobileNav() {
      body.classList.remove('menu-open');
      if (mobileToggle) mobileToggle.setAttribute('aria-expanded', 'false');
    }

    if (mobileToggle) {
      mobileToggle.setAttribute('aria-expanded', 'false');
      mobileToggle.addEventListener('click', function (event) {
        event.preventDefault();
        body.classList.toggle('menu-open');
        mobileToggle.setAttribute('aria-expanded', String(body.classList.contains('menu-open')));
      });
    }
    if (overlay) overlay.addEventListener('click', closeMobileNav);
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') closeMobileNav();
    });
  }

  function initialiseThemeToggle() {
    var toggle = document.querySelector('.fleetng-theme-toggle');
    if (!toggle) return;
    toggle.addEventListener('click', function () {
      setColourTheme(root.getAttribute('data-fleetng-theme') === 'dark' ? 'light' : 'dark');
    });
  }

  function initialisePasswordToggles() {
    document.querySelectorAll('.fleetng-password-toggle').forEach(function (wrapper) {
      var input = wrapper.querySelector('input');
      var button = wrapper.querySelector('button, .input-group-text');
      if (!input || !button) return;
      button.addEventListener('click', function () {
        var showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';
        button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
      });
    });
  }

  function createSearchItem(item) {
    var link = document.createElement('a');
    link.className = 'fleetng-search-item';
    link.href = item.url;
    link.setAttribute('role', 'option');
    var label = document.createElement('span');
    label.textContent = item.label;
    var meta = document.createElement('small');
    meta.textContent = item.meta || '';
    link.appendChild(label);
    link.appendChild(meta);
    return link;
  }

  function renderSearchResults(container, groups) {
    container.replaceChildren();
    var hasResults = false;
    Object.keys(groups).forEach(function (key) {
      var items = groups[key] || [];
      if (!items.length) return;
      hasResults = true;
      var title = document.createElement('div');
      title.className = 'fleetng-search-group-label';
      title.textContent = key;
      container.appendChild(title);
      items.forEach(function (item) { container.appendChild(createSearchItem(item)); });
    });
    if (!hasResults) {
      var empty = document.createElement('div');
      empty.className = 'fleetng-search-message';
      empty.textContent = 'No matching records found.';
      container.appendChild(empty);
    }
  }

  function initialiseGlobalSearch() {
    var shell = document.querySelector('.fleetng-global-search');
    if (!shell) return;
    var input = shell.querySelector('input');
    var results = shell.querySelector('.fleetng-search-results');
    var endpoint = shell.getAttribute('data-search-url');
    var timer;
    var request;

    function open() {
      shell.classList.add('search-open');
      input.focus();
    }

    function close() {
      results.hidden = true;
      input.setAttribute('aria-expanded', 'false');
      shell.classList.remove('search-open');
    }

    shell.addEventListener('click', function (event) {
      if (event.target !== input && window.innerWidth < 768) open();
    });
    document.addEventListener('keydown', function (event) {
      if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        open();
      }
      if (event.key === 'Escape' && shell.contains(document.activeElement)) close();
    });
    document.addEventListener('click', function (event) {
      if (!shell.contains(event.target)) close();
    });

    input.addEventListener('input', function () {
      window.clearTimeout(timer);
      if (request) request.abort();
      var query = input.value.trim();
      if (query.length < 2) {
        results.hidden = true;
        input.setAttribute('aria-expanded', 'false');
        return;
      }
      timer = window.setTimeout(function () {
        var loading = document.createElement('div');
        loading.className = 'fleetng-search-message';
        loading.textContent = 'Searching...';
        results.replaceChildren(loading);
        results.hidden = false;
        input.setAttribute('aria-expanded', 'true');
        request = new AbortController();
        fetch(endpoint + '?q=' + encodeURIComponent(query), {
          headers: { Accept: 'application/json' },
          signal: request.signal,
          credentials: 'same-origin'
        })
          .then(function (response) {
            if (!response.ok) throw new Error('Search failed');
            return response.json();
          })
          .then(function (payload) { renderSearchResults(results, payload.groups || {}); })
          .catch(function (error) {
            if (error.name === 'AbortError') return;
            var message = document.createElement('div');
            message.className = 'fleetng-search-message';
            message.textContent = 'Search is unavailable. Please try again.';
            results.replaceChildren(message);
          });
      }, 280);
    });
  }

  function improveTables() {
    document.querySelectorAll('table.table').forEach(function (table) {
      if (!table.getAttribute('aria-label')) {
        var card = table.closest('.card');
        var heading = card && card.querySelector('.card-title');
        table.setAttribute('aria-label', heading ? heading.textContent.trim() : 'Data table');
      }
      table.querySelectorAll('thead th').forEach(function (header) {
        header.setAttribute('scope', 'col');
      });
    });

    document.querySelectorAll('.dataTables_wrapper').forEach(function (wrapper) {
      if (wrapper.querySelector('.fleetng-density-controls')) return;
      var toolbar = wrapper.querySelector('.row');
      if (!toolbar) return;
      var controls = document.createElement('div');
      controls.className = 'fleetng-density-controls ml-auto';
      controls.setAttribute('aria-label', 'Table density');
      controls.innerHTML = '<button type="button" class="btn btn-sm btn-outline-secondary" data-density="cozy">Cozy</button>' +
        '<button type="button" class="btn btn-sm btn-outline-secondary" data-density="compact">Compact</button>';
      controls.addEventListener('click', function (event) {
        var button = event.target.closest('[data-density]');
        if (!button) return;
        wrapper.classList.toggle('fleetng-density-compact', button.dataset.density === 'compact');
      });
      toolbar.appendChild(controls);
    });
  }

  function FleetTable(element) {
    this.element = element;
    this.endpoint = element.dataset.endpoint;
    this.columns = JSON.parse(element.dataset.columns || '[]');
    this.emptyMessage = element.dataset.emptyMessage || 'No records found.';
    this.head = element.querySelector('[data-table-head]');
    this.body = element.querySelector('[data-table-body]');
    this.stateMessage = element.querySelector('[data-table-state]');
    this.summary = element.querySelector('[data-table-summary]');
    this.pageLabel = element.querySelector('[data-table-page-label]');
    this.search = element.querySelector('[data-table-search]');
    this.pageSize = element.querySelector('[data-table-page-size]');
    var params = new URLSearchParams(window.location.search);
    this.state = {
      page: Math.max(1, Number(params.get('page')) || 1),
      perPage: [10, 25, 50, 100].includes(Number(params.get('per_page'))) ? Number(params.get('per_page')) : 10,
      sort: params.get('sort') || '',
      direction: params.get('direction') === 'asc' ? 'asc' : 'desc',
      search: params.get('search') || '',
      lastPage: 1
    };
    this.request = null;
    this.searchTimer = null;
    this.renderHead();
    this.bind();
    this.search.value = this.state.search;
    this.pageSize.value = String(this.state.perPage);
    this.load();
  }

  FleetTable.prototype.renderHead = function () {
    var table = this;
    var selectHeader = document.createElement('th');
    var selectAll = document.createElement('input');
    selectAll.type = 'checkbox';
    selectAll.dataset.tableSelectAll = 'true';
    selectAll.setAttribute('aria-label', 'Select all rows on this page');
    selectHeader.appendChild(selectAll);
    this.head.appendChild(selectHeader);
    this.columns.forEach(function (column) {
      var th = document.createElement('th');
      th.scope = 'col';
      if (column.sortable) {
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'fleetng-sort-button';
        button.dataset.sort = column.sort || column.key;
        button.textContent = column.label;
        button.setAttribute('aria-sort', 'none');
        th.appendChild(button);
      } else {
        th.textContent = column.label;
      }
      table.head.appendChild(th);
    });
  };

  FleetTable.prototype.bind = function () {
    var table = this;
    this.head.addEventListener('click', function (event) {
      var button = event.target.closest('[data-sort]');
      if (!button) return;
      var nextSort = button.dataset.sort;
      table.state.direction = table.state.sort === nextSort && table.state.direction === 'asc' ? 'desc' : 'asc';
      table.state.sort = nextSort;
      table.state.page = 1;
      table.load();
    });
    this.search.addEventListener('input', function () {
      window.clearTimeout(table.searchTimer);
      table.searchTimer = window.setTimeout(function () {
        table.state.search = table.search.value.trim();
        table.state.page = 1;
        table.load();
      }, 300);
    });
    this.pageSize.addEventListener('change', function () {
      table.state.perPage = Number(table.pageSize.value);
      table.state.page = 1;
      table.load();
    });
    this.element.addEventListener('click', function (event) {
      var pageButton = event.target.closest('[data-table-page]');
      if (pageButton) {
        var action = pageButton.dataset.tablePage;
        if (action === 'first') table.state.page = 1;
        if (action === 'previous') table.state.page = Math.max(1, table.state.page - 1);
        if (action === 'next') table.state.page = Math.min(table.state.lastPage, table.state.page + 1);
        if (action === 'last') table.state.page = table.state.lastPage;
        table.load();
      }
      var density = event.target.closest('[data-table-density]');
      if (density) {
        table.element.classList.toggle('fleetng-density-compact', density.dataset.tableDensity === 'compact');
        table.element.querySelectorAll('[data-table-density]').forEach(function (button) {
          button.classList.toggle('active', button === density);
        });
      }
      var retry = event.target.closest('[data-table-retry]');
      if (retry) table.load();
      var selectAll = event.target.closest('[data-table-select-all]');
      if (selectAll) {
        table.element.querySelectorAll('.fleetng-row-selector').forEach(function (checkbox) {
          checkbox.checked = selectAll.checked;
        });
      }
      var rowAction = event.target.closest('[data-row-action]');
      if (rowAction && rowAction.dataset.rowAction === 'delete-client') {
        var field = document.getElementById('modern_client_id');
        if (field) field.value = rowAction.dataset.value;
        if (window.jQuery) window.jQuery('#deleteClientConfirm').modal('show');
      }
    });
  };

  FleetTable.prototype.syncUrl = function () {
    var params = new URLSearchParams(window.location.search);
    [['page', this.state.page], ['per_page', this.state.perPage], ['sort', this.state.sort], ['direction', this.state.direction], ['search', this.state.search]].forEach(function (entry) {
      if (entry[1]) params.set(entry[0], entry[1]); else params.delete(entry[0]);
    });
    window.history.replaceState({}, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));
  };

  FleetTable.prototype.setMessage = function (message, retry) {
    this.body.replaceChildren();
    this.stateMessage.hidden = false;
    this.stateMessage.replaceChildren(document.createTextNode(message));
    if (retry) {
      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'btn btn-sm btn-outline-secondary ml-1';
      button.dataset.tableRetry = 'true';
      button.textContent = 'Retry';
      this.stateMessage.appendChild(button);
    }
  };

  FleetTable.prototype.renderCell = function (cell, column, row) {
    var value = row[column.key];
    if (column.type === 'status') {
      var badge = document.createElement('span');
      badge.className = 'fleetng-status fleetng-status-' + String(value || 'unknown').toLowerCase().replace(/[^a-z0-9]+/g, '-');
      badge.textContent = value || 'Unknown';
      cell.appendChild(badge);
      return;
    }
    if (column.type === 'actions') {
      var actions = document.createElement('div');
      actions.className = 'fleetng-row-actions';
      (value || []).forEach(function (action) {
        var control = document.createElement(action.url ? 'a' : 'button');
        control.className = 'btn btn-sm ' + (action.danger ? 'btn-outline-danger' : 'btn-outline-secondary');
        control.textContent = action.label;
        if (action.url) control.href = action.url;
        else {
          control.type = 'button';
          control.dataset.rowAction = action.action;
          control.dataset.value = action.value;
        }
        actions.appendChild(control);
      });
      cell.appendChild(actions);
      return;
    }
    cell.textContent = value == null || value === '' ? '—' : String(value);
  };

  FleetTable.prototype.render = function (payload) {
    var table = this;
    var rows = payload.data || [];
    var meta = payload.meta || {};
    this.body.replaceChildren();
    var selectAll = this.element.querySelector('[data-table-select-all]');
    if (selectAll) selectAll.checked = false;
    this.state.lastPage = Math.max(1, Number(meta.last_page) || 1);
    this.state.page = Math.min(this.state.page, this.state.lastPage);
    if (!rows.length) {
      this.setMessage(this.emptyMessage, false);
    } else {
      this.stateMessage.hidden = true;
      rows.forEach(function (row) {
        var tr = document.createElement('tr');
        var selectorCell = document.createElement('td');
        var selector = document.createElement('input');
        selector.type = 'checkbox';
        selector.className = 'fleetng-row-selector';
        selector.setAttribute('aria-label', 'Select row ' + row.id);
        selectorCell.appendChild(selector);
        tr.appendChild(selectorCell);
        table.columns.forEach(function (column) {
          var td = document.createElement('td');
          td.dataset.label = column.label;
          table.renderCell(td, column, row);
          tr.appendChild(td);
        });
        table.body.appendChild(tr);
      });
    }
    var first = rows.length ? ((meta.page - 1) * meta.per_page) + 1 : 0;
    var last = rows.length ? first + rows.length - 1 : 0;
    this.summary.textContent = 'Showing ' + first + '–' + last + ' of ' + (meta.total || 0);
    this.pageLabel.textContent = 'Page ' + this.state.page + ' of ' + this.state.lastPage;
    this.element.querySelectorAll('[data-table-page]').forEach(function (button) {
      var action = button.dataset.tablePage;
      button.disabled = (table.state.page === 1 && (action === 'first' || action === 'previous')) ||
        (table.state.page === table.state.lastPage && (action === 'next' || action === 'last'));
    });
    this.head.querySelectorAll('[data-sort]').forEach(function (button) {
      button.setAttribute('aria-sort', button.dataset.sort === table.state.sort ? table.state.direction + 'ending' : 'none');
    });
    this.syncUrl();
  };

  FleetTable.prototype.load = function () {
    var table = this;
    if (this.request) this.request.abort();
    this.setMessage('Loading records...', false);
    var params = new URLSearchParams({
      format: 'modern',
      page: this.state.page,
      per_page: this.state.perPage,
      direction: this.state.direction
    });
    if (this.state.sort) params.set('sort', this.state.sort);
    if (this.state.search) params.set('search', this.state.search);
    this.request = new AbortController();
    fetch(this.endpoint + '?' + params.toString(), {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      signal: this.request.signal
    })
      .then(function (response) {
        if (!response.ok) throw new Error('Request failed');
        return response.json();
      })
      .then(function (payload) { table.render(payload); })
      .catch(function (error) {
        if (error.name !== 'AbortError') table.setMessage('Unable to load records.', true);
      });
  };

  document.addEventListener('DOMContentLoaded', function () {
    initialiseNavigation();
    initialiseThemeToggle();
    initialisePasswordToggles();
    initialiseGlobalSearch();
    document.querySelectorAll('[data-fleetng-table]').forEach(function (table) { new FleetTable(table); });
    refreshIcons();
    improveTables();
    window.setTimeout(improveTables, 500);
  });
}());
