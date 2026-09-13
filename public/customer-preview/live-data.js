(function () {
  'use strict';
  var signedIn = false;
  function apiBaseUrl() {
    return (window.CustomerPortalConfig && window.CustomerPortalConfig.apiBaseUrl) || '/customer-portal/api';
  }
  async function request(method, path, data) {
    var multipart = data instanceof FormData;
    var response = await fetch(apiBaseUrl() + path, {
      method: method, credentials: 'same-origin',
      headers: Object.assign({ 'Accept':'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content },multipart ? {} : {'Content-Type':'application/json'}),
      body: method === 'GET' ? undefined : multipart ? data : JSON.stringify(data || {})
    });
    var csrf = response.headers.get('X-CSRF-TOKEN');
    if (csrf) document.querySelector('meta[name="csrf-token"]').content = csrf;
    var payload;
    try {
      payload = await response.json();
    } catch (_) {
      throw new Error(response.status === 503 ? 'Customer verification is not enabled on this server.' : 'The server returned an unreadable response. Try again.');
    }
    if (!response.ok || payload.status === false) {
      if (response.status === 401) signedIn = false;
      var error = new Error(response.status === 419 ? 'Your session expired. Reload and sign in again.' : payload.message || payload.error || 'The request could not be completed.');
      error.errors = payload.errors || {}; throw error;
    }
    if (path === '/login' || path === '/register' || path === '/profile') signedIn = true;
    if (path === '/merchants') payload.data = Object.values(payload.data || {}).filter(Boolean).map(function (m) {
      var drivers = Object.values(m.drivers || {}).map(function (d) {
        return Object.assign({},d,{full_name:d.full_name || d.name || [d.first_name,d.last_name].filter(Boolean).join(' ')});
      });
      return Object.assign({},m,{drivers:drivers});
    });
    if (path === '/draft/trips') payload.data = (payload.data || []).map(function (d) { return Object.assign({},d,{status:'Draft'}); });
    return payload;
  }
  window.CustomerPreview = {
    mode:'local', request:request,
    avatar:function (profile) { return profile.profile_image ? (String(profile.profile_image).startsWith('https://') ? profile.profile_image : apiBaseUrl() + '/profile-image?v=' + encodeURIComponent(profile.updated_at || '')) : ''; },
    ready:request('GET','/profile').catch(function () { signedIn = false; }),
    isSignedIn:function () { return signedIn; },
    total:function (t) { return Number(t.total_cost || 0) + Number(t.cost_of_sand || 0) + Number(t.road_money || 0); },
    logout:async function () { await request('POST','/logout'); signedIn = false; },
    reset:function () { throw new Error('Local database records are not reset from this interface.'); }
  };
}());
