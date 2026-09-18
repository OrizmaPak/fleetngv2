(function () {
  'use strict';
  const api = window.CustomerPreview;
  const local = api.mode === 'local';
  let pendingLogin = null;
  let returnRoute = null;
  const root = document.getElementById('app');
  const dialog = document.getElementById('dialog');
  const e = value => String(value ?? '').replace(/[&<>"']/g, c => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]));
  const icon = name => '<i data-feather="' + name + '" aria-hidden="true"></i>';
  const money = value => 'NGN ' + new Intl.NumberFormat('en-NG', { maximumFractionDigits:0 }).format(value || 0);
  const shortDate = value => new Intl.DateTimeFormat('en-GB', { day:'2-digit', month:'short', year:'numeric' }).format(new Date(value));
  const time = value => new Intl.DateTimeFormat('en-GB', { hour:'2-digit', minute:'2-digit' }).format(new Date(value));
  const badge = status => '<span class="status ' + e(String(status).toLowerCase()) + '">' + e(status) + '</span>';
  const initials = p => ((p.first_name || '').slice(0,1) + (p.last_name || '').slice(0,1)).toUpperCase();
  const linkButton = (url, label, symbol = 'plus', secondary = false) => '<a class="button' + (secondary ? ' secondary' : '') + '" href="#/' + url + '">' + icon(symbol) + e(label) + '</a>';
  const request = async (method, path, data) => (await api.request(method, path, data)).data;
  let profile, trips = [], drafts = [], payments = [], merchants = [], locations = [];
  let route = '', tableState = { search:'', status:'All', payment:'All', sort:'date', direction:'desc', page:1, size:5, compact:false }, selected = new Set();
  let booking = null, loginMode = 'existing', renderId = 0, toastTimer;
  function icons() { if (window.feather) window.feather.replace({ 'stroke-width':1.6 }); }
  function toast(message) { const node = document.getElementById('toast'); node.textContent = message; node.classList.add('visible'); clearTimeout(toastTimer); toastTimer = setTimeout(() => node.classList.remove('visible'), 4500); }
  function go(path) { location.hash = '#/' + path; }
  function heading(title, description, action = '') { return '<div class="heading"><div><h1>' + e(title) + '</h1><p>' + e(description) + '</p></div>' + action + '</div>'; }
  function row(label, value, raw = false) { return '<div class="detail-row"><span>' + e(label) + '</span><strong>' + (raw ? value : e(value || 'Not assigned')) + '</strong></div>'; }
  function empty(title, description, action = '') { return '<div class="empty">' + icon('inbox') + '<h3>' + e(title) + '</h3><p>' + e(description) + '</p>' + action + '</div>'; }
  function field(label, name, type = 'text', value = '', extra = '', span = false) { return '<div class="field' + (span ? ' span-2' : '') + '"><label for="' + name + '">' + label + '</label><input id="' + name + '" name="' + name + '" type="' + type + '" value="' + e(value) + '" ' + extra + '></div>'; }
  function portalConfig() { return window.CustomerPortalConfig || {}; }
  function testOtpEnabled() { return !!portalConfig().testOtpEnabled; }
  function verificationLabel() { return testOtpEnabled() ? 'Testing verification / OTP 123456' : 'Phone verification'; }
  function verificationNote() { return testOtpEnabled() ? 'Testing verification: use OTP 123456. No SMS is sent.' : 'Phone verification is required before customer access.'; }
  function logoUrl() { return portalConfig().logoUrl || '../images/logo/fleetng-logo.svg'; }
  function loginImageUrl() { return portalConfig().loginImageUrl || '../front/images/hero-img.png'; }
  function homeUrl() { return portalConfig().homeUrl || '../'; }
  function brand() { return '<a class="brand" href="#/overview"><img src="' + e(logoUrl()) + '" alt="FleetNG logo"><span>FLEETNG</span></a>'; }
  function shell(title, content) {
    const nav = [['overview','Overview','grid'],['trips','My trips','truck'],['drafts','Draft bookings','file-text'],['payments','Payments','credit-card'],['profile','My account','user']];
    const active = route.startsWith('trip/') ? 'trips' : route.startsWith('draft/') ? 'drafts' : route;
    root.innerHTML = '<button class="overlay" data-action="close-nav" aria-label="Close navigation"></button><aside class="sidebar">' + brand() + '<p class="sidebar-caption">Customer workspace</p><nav aria-label="Main navigation">' + nav.map(([url,label,i]) => '<a class="nav-link ' + (url === active ? 'active' : '') + '" ' + (url === active ? 'aria-current="page"' : '') + ' href="#/' + url + '">' + icon(i) + e(label) + (url === 'drafts' && drafts.length ? '<span class="nav-count">' + drafts.length + '</span>' : '') + '</a>').join('') + '</nav><div class="sidebar-foot"><div class="muted">FleetNG customer portal</div><button class="text-button" data-action="reset">' + icon('rotate-ccw') + 'Reset sample data</button><button class="text-button" data-action="logout">' + icon('log-out') + 'Sign out</button></div></aside><div class="workspace"><header class="topbar"><div class="breadcrumb"><button class="icon-button mobile-toggle" data-action="nav" aria-label="Open navigation" aria-expanded="false">' + icon('menu') + '</button><span class="root-label">Customer portal</span>' + icon('chevron-right') + '<span>' + e(title) + '</span></div><div class="topbar-right"><span class="demo-tag">' + icon('flask-conical').replace('flask-conical','box') + 'Demo / sample data</span><a class="identity" href="#/profile"><span class="avatar">' + e(initials(profile)) + '</span><div><strong>' + e(profile.first_name + ' ' + profile.last_name) + '</strong><small>Customer account</small></div></a></div></header><main id="main" tabindex="-1">' + content + '</main></div>';
    root.querySelector('.breadcrumb').insertAdjacentHTML('afterbegin','<a class="mobile-brand" href="#/overview" aria-label="FleetNG overview"><img src="' + e(logoUrl()) + '" alt="FleetNG"></a>');
    if (local) {
      root.querySelector('[data-action="reset"]').remove();
      root.querySelector('.topbar .demo-tag').textContent = verificationLabel();
      const accountTag = root.querySelector('.profile-aside .demo-tag');
      if (accountTag) accountTag.textContent = 'Customer account';
      const confirm = root.querySelector('[name="intent"][value="confirm"]');
      if (confirm) confirm.textContent = 'Confirm booking';
      const reviewNote = root.querySelector('#booking-form .review-note');
      if (reviewNote && booking.step === 3) reviewNote.textContent = 'This saves a booking in the local database. A driver is required to confirm.';
    }
    const profileFormNode = root.querySelector('#profile-form');
    if (profileFormNode) {
      profileFormNode.querySelector('.form-grid').insertAdjacentHTML('afterbegin','<div class="field span-2"><label for="profile_image">Profile photo</label><input type="file" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/webp"><small>JPEG, PNG or WebP, up to 2 MB</small></div>');
      const photo = api.avatar ? api.avatar(profile) : profile.profile_image;
      if (photo && /^(https:\/\/|\/|data:image\/)/.test(photo)) root.querySelector('.profile-aside .avatar').innerHTML = '<img class="profile-photo" src="' + e(photo) + '" alt="Your profile photo">';
    }
    document.body.classList.remove('nav-open'); icons();
  }
  const eligible = t => !t.payment_id && !['Canceled','Declined'].includes(t.status);
  function overview() {
    const completed = trips.filter(t => t.status === 'Completed').length;
    const active = trips.filter(t => ['New','Running'].includes(t.status)).length;
    const unpaid = trips.filter(eligible), outstanding = unpaid.reduce((s,t) => s + api.total(t), 0);
    const metrics = [['All trips',trips.length,'Across your account','truck',''],['Active bookings',active,'New and running trips','navigation','cyan'],['Completed trips',completed,'Delivered to destination','check-circle','green'],['Outstanding',money(outstanding),unpaid.length + ' unpaid bookings','credit-card','amber']];
    const recent = [...trips].sort((a,b) => new Date(b.pick_up_datetime) - new Date(a.pick_up_datetime)).slice(0,4);
    shell('Overview', heading('Good day, ' + profile.first_name, 'Your bookings, deliveries and payments in one place.', linkButton('book','Book a trip')) + '<section class="metrics" aria-label="Account summary">' + metrics.map(([label,value,sub,i,color]) => '<div class="metric"><div class="metric-label">' + label + icon(i) + '</div><strong class="metric-value ' + color + '">' + e(value) + '</strong><small>' + sub + '</small></div>').join('') + '</section><section><div class="section-title"><h2>Recent trips</h2><a href="#/trips">View all trips ' + icon('arrow-up-right') + '</a></div>' + simpleTrips(recent) + '</section><div class="overview-bottom"><section><div class="section-title"><h2>Recent payments</h2><a href="#/payments">Payment history ' + icon('arrow-up-right') + '</a></div>' + (payments.length ? '<div class="activity-list">' + [...payments].sort((a,b) => new Date(b.created_at)-new Date(a.created_at)).slice(0,3).map(p => '<div class="activity"><span class="activity-icon">' + icon('check') + '</span><div><strong>' + money(p.amount) + '</strong><p>Trip #' + p.trip_id + ' &middot; ' + e(p.payment_type) + '</p></div><time>' + shortDate(p.created_at) + '</time></div>').join('') + '</div>' : empty('No payments yet','Payments will appear here.')) + '</section><section><div class="section-title"><h2>Ready when you are</h2></div><div class="summary-band"><span class="eyebrow">Draft bookings</span><strong class="summary-value">' + drafts.length + ' saved</strong><p class="muted">' + (drafts.length ? 'Your next booking is waiting to be completed.' : 'Start a booking and save it for later.') + '</p>' + linkButton(drafts.length ? 'drafts' : 'book',drafts.length ? 'Continue a booking' : 'Book a trip','arrow-right',true) + '</div></section></div>');
  }
  function tripCells(t, draft = false) {
    return '<td><a href="#/' + (draft ? 'draft/' : 'trip/') + t.id + '"><strong>#' + t.id + '</strong></a><small>' + shortDate(t.pick_up_datetime) + ' &middot; ' + time(t.pick_up_datetime) + '</small></td><td class="route-cell"><strong title="' + e(t.pickup_location) + '">' + e(t.pickup_location_alias || t.pickup_location) + '</strong><small title="' + e(t.drop_location) + '">' + icon('arrow-down-right') + e(t.drop_location) + '</small></td><td>' + e(t.driver || 'Not assigned') + '<small>' + e(t.truck_number || 'Choose a driver later') + '</small></td><td>' + badge(t.status) + '</td><td class="money">' + money(api.total(t)) + '<small>' + (draft ? 'Proposed cost' : t.payment_id ? 'Paid' : 'Unpaid') + '</small></td><td><a class="icon-button" href="#/' + (draft ? 'draft/' : 'trip/') + t.id + '" aria-label="' + (draft ? 'Edit draft ' : 'View trip ') + t.id + '" title="' + (draft ? 'Edit draft' : 'View trip') + '">' + icon('arrow-up-right') + '</a></td>';
  }
  function simpleTrips(data) {
    return data.length ? '<div class="table-wrap"><div class="table-scroll"><table><caption class="skip">Recent trips</caption><thead><tr><th>Trip / pickup</th><th>Route</th><th>Driver / vehicle</th><th>Status</th><th class="money">Amount</th><th><span class="skip">Actions</span></th></tr></thead><tbody>' + data.map(t => '<tr>' + tripCells(t) + '</tr>').join('') + '</tbody></table></div></div>' : empty('No trips yet','Your bookings will appear here.',linkButton('book','Book a trip'));
  }
  function tablePage() {
    const isDraft = route === 'drafts', isPayment = route === 'payments';
    const title = isDraft ? 'Draft bookings' : isPayment ? 'Payments' : 'My trips';
    const description = isDraft ? 'Review and finish your saved bookings.' : isPayment ? 'A record of payments for your trips.' : 'Follow every booking from pickup to completion.';
    shell(title, heading(title,description,linkButton('book','Book a trip')) + '<div id="table-area"></div>');
    renderTable();
  }
  function tableData() {
    const source = route === 'drafts' ? drafts : route === 'payments' ? payments : trips;
    return source.filter(t => {
      const text = route === 'payments' ? [t.transaction_id,t.trip_id,t.payment_type] : [t.id,t.pickup_location,t.pickup_location_alias,t.drop_location,t.driver,t.truck_number];
      return text.join(' ').toLowerCase().includes(tableState.search.toLowerCase()) && (tableState.status === 'All' || t.status === tableState.status) && (tableState.payment === 'All' || Boolean(t.payment_id) === (tableState.payment === 'Paid'));
    }).sort((a,b) => {
      const value = x => tableState.sort === 'amount' ? (route === 'payments' ? x.amount : api.total(x)) : tableState.sort === 'id' ? x.id : new Date(x.pick_up_datetime || x.created_at).getTime();
      return (value(a)-value(b) || a.id-b.id) * (tableState.direction === 'asc' ? 1 : -1);
    });
  }
  function sortHead(label,key) { return '<th aria-sort="' + (tableState.sort === key ? tableState.direction === 'asc' ? 'ascending' : 'descending' : 'none') + '"><button class="sort-button" data-sort="' + key + '">' + label + icon(tableState.sort === key ? tableState.direction === 'asc' ? 'arrow-up' : 'arrow-down' : 'chevron-down') + '</button></th>'; }
  function renderTable() {
    const target = document.getElementById('table-area'); if (!target) return;
    const draft = route === 'drafts', payment = route === 'payments', selectable = route === 'trips';
    const all = tableData(), pages = Math.max(1,Math.ceil(all.length/tableState.size));
    tableState.page = Math.min(tableState.page,pages);
    const visible = all.slice((tableState.page-1)*tableState.size,tableState.page*tableState.size);
    const selectedTrips = trips.filter(t => selected.has(t.id) && eligible(t));
    const tabs = ['All','New','Running','Completed','Canceled','Declined'];
    target.innerHTML = '<div class="toolbar">' + (selectable ? '<div class="tabs" role="tablist" aria-label="Trip status">' + tabs.map(s => '<button class="tab" role="tab" aria-selected="' + (tableState.status === s) + '" data-status="' + s + '">' + s + '</button>').join('') + '</div>' : '<span class="muted">' + all.length + ' ' + (draft ? 'draft bookings' : 'transactions') + '</span>') + '<div class="tools"><label class="search">' + icon('search') + '<input id="table-search" aria-label="Search ' + route + '" placeholder="Search ' + route + '" value="' + e(tableState.search) + '"></label>' + (selectable ? '<select id="payment-filter" aria-label="Payment status">' + ['All','Paid','Unpaid'].map(v => '<option ' + (v === tableState.payment ? 'selected' : '') + ' value="' + v + '">' + (v === 'All' ? 'All payments' : v) + '</option>').join('') + '</select>' : '') + '<button class="icon-button" data-action="density" aria-label="Compact rows" title="Compact rows" aria-pressed="' + tableState.compact + '">' + icon('align-justify') + '</button></div></div>' + (selectedTrips.length ? '<div class="bulk-bar"><span>' + selectedTrips.length + ' selected &middot; ' + money(selectedTrips.reduce((s,t) => s+api.total(t),0)) + '</span><div class="tools"><button class="text-button" data-action="clear-selection">Clear selection</button><button class="button" data-action="pay-selected">' + icon('credit-card') + 'Pay selected</button></div></div>' : '') + '<div class="table-wrap"><div class="table-scroll"><table class="' + (tableState.compact ? 'compact' : '') + '"><caption class="skip">' + e(route) + '</caption><thead><tr>' + (selectable ? '<th><input type="checkbox" id="select-page" aria-label="Select eligible trips on this page" ' + (!visible.some(eligible) ? 'disabled' : '') + '></th>' : '') + (payment ? sortHead('Transaction','id') + '<th>Trip</th><th>Method</th>' + sortHead('Date','date') + '<th>Status</th>' + sortHead('Amount','amount') : sortHead('Trip / pickup','date') + '<th>Route</th><th>Driver / vehicle</th><th>Status</th>' + sortHead('Amount','amount') + '<th><span class="skip">Actions</span></th>') + '</tr></thead><tbody>' + visible.map(t => '<tr>' + (selectable ? '<td><input type="checkbox" data-select="' + t.id + '" aria-label="Select trip ' + t.id + '" ' + (selected.has(t.id) ? 'checked ' : '') + (!eligible(t) ? 'disabled' : '') + '></td>' : '') + (payment ? '<td><strong>' + e(t.transaction_id) + '</strong><small>' + e(t.reference_code) + '</small></td><td><a href="#/trip/' + t.trip_id + '">#' + t.trip_id + '</a></td><td>' + e(t.payment_type) + '</td><td>' + shortDate(t.created_at) + '</td><td>' + badge(t.status) + '</td><td class="money">' + money(t.amount) + '</td>' : tripCells(t,draft)) + '</tr>').join('') + '</tbody></table>' + (!visible.length ? empty('No matching ' + route,'Try another search or filter.') : '') + '</div><div class="table-footer"><span aria-live="polite">' + (all.length ? (tableState.page-1)*tableState.size+1 : 0) + '-' + Math.min(tableState.page*tableState.size,all.length) + ' of ' + all.length + ' records</span><div class="pager"><label>Rows <select id="page-size">' + [5,10,25,50].map(n => '<option ' + (n === tableState.size ? 'selected' : '') + '>' + n + '</option>').join('') + '</select></label>' + [['first','chevrons-left'],['previous','chevron-left']].map(([p,i]) => '<button class="icon-button" data-page="' + p + '" aria-label="' + p + ' page" ' + (tableState.page === 1 ? 'disabled' : '') + '>' + icon(i) + '</button>').join('') + '<span>' + tableState.page + ' / ' + pages + '</span>' + [['next','chevron-right'],['last','chevrons-right']].map(([p,i]) => '<button class="icon-button" data-page="' + p + '" aria-label="' + p + ' page" ' + (tableState.page === pages ? 'disabled' : '') + '>' + icon(i) + '</button>').join('') + '</div></div></div>';
    const selectPage = document.getElementById('select-page');
    if (selectPage) { const eligibleRows = visible.filter(eligible), count = eligibleRows.filter(t => selected.has(t.id)).length; selectPage.checked = count > 0 && count === eligibleRows.length; selectPage.indeterminate = count > 0 && count < eligibleRows.length; }
    icons();
  }
  function localDateTime(value) { const d = new Date(value); d.setMinutes(d.getMinutes()-d.getTimezoneOffset()); return d.toISOString().slice(0,16); }
  function newBooking(trip) {
    const tomorrow = new Date(); tomorrow.setDate(tomorrow.getDate()+1); tomorrow.setHours(9,0,0,0);
    return { step:1, id:trip ? trip.id : null, pickup_location:trip ? String(trip.pickup_location_id) : '', pickup_datetime:localDateTime(trip ? trip.pick_up_datetime : tomorrow), drop_off_location:trip ? trip.drop_location : '', cost:trip ? String(trip.total_cost) : '', merchant:trip && trip.merchant_id ? String(trip.merchant_id) : '', driver:trip && trip.driver_id ? String(trip.driver_id) : '' };
  }
  function locationOptions() { return '<option value="">Choose a pickup location</option>' + locations.map(l => '<option value="' + l.id + '" ' + (String(l.id) === booking.pickup_location ? 'selected' : '') + '>' + e(l.location_name + ' / ' + l.location) + '</option>').join(''); }
  function bookingSummary() {
    const l = locations.find(l => String(l.id) === booking.pickup_location);
    const validDate = Number.isFinite(new Date(booking.pickup_datetime).getTime());
    return '<aside class="booking-summary"><h3>Booking summary</h3><div class="route-point">' + icon('circle') + '<div><small>PICKUP</small><strong>' + e(l ? l.location_name : 'Choose a location') + '</strong></div></div><div class="route-point">' + icon('map-pin') + '<div><small>DROP-OFF</small><strong>' + e(booking.drop_off_location || 'Add a destination') + '</strong></div></div>' + row('Pickup date',validDate ? shortDate(booking.pickup_datetime) : 'Choose a date') + row('Time',validDate ? time(booking.pickup_datetime) : 'Choose a time') + '<div class="detail-row total"><span>Proposed trip cost</span><strong>' + money(booking.cost) + '</strong></div></aside>';
  }
  function bookingPage() {
    let body = '';
    if (booking.step === 1) body = '<div class="form-grid"><div class="field span-2"><label for="pickup_location">Pickup location</label><select id="pickup_location" name="pickup_location" required>' + locationOptions() + '</select></div>' + field('Drop-off location','drop_off_location','text',booking.drop_off_location,'required maxlength="255" placeholder="Address or project site"',true) + field('Pickup date and time','pickup_datetime','datetime-local',booking.pickup_datetime,'required min="' + localDateTime(new Date()).slice(0,10) + 'T00:00"') + field('Proposed trip cost (NGN)','cost','number',booking.cost,'required min="0" step="1" placeholder="85000"') + '</div>';
    else if (booking.step === 2) body = '<div class="field"><label for="merchant">Merchant</label><select id="merchant" name="merchant"><option value="">Assign later</option>' + merchants.map(m => '<option value="' + m.id + '" ' + (booking.merchant === String(m.id) ? 'selected' : '') + '>' + e(m.name) + '</option>').join('') + '</select></div><div id="driver-options">' + driverOptions() + '</div>';
    else {
      const merchant = merchants.find(m => String(m.id) === booking.merchant), driver = merchant && merchant.drivers.find(d => String(d.id) === booking.driver);
      body = '<h2>Review your booking</h2><div class="review-list">' + row('Customer',profile.first_name + ' ' + profile.last_name) + row('Phone',profile.country_code + ' ' + profile.phone_number) + row('Merchant',merchant && merchant.name) + row('Driver',driver && driver.full_name) + row('Vehicle',driver && driver.vehicle_id) + '</div><p class="review-note">Demo booking only. No driver will be contacted and no payment will be taken.</p>';
    }
    shell(booking.id ? 'Edit draft' : 'Book a trip',heading(booking.id ? 'Finish your booking' : 'Book a trip','Arrange your next pickup and delivery.',linkButton('drafts','Saved drafts','file-text',true)) + '<div class="stepper" aria-label="Booking progress">' + ['Trip details','Merchant & driver','Review'].map((label,i) => '<div class="step ' + (booking.step === i+1 ? 'active' : '') + '" ' + (booking.step === i+1 ? 'aria-current="step"' : '') + '><b>' + (i+1) + '</b>' + label + '</div>').join('') + '</div><div class="booking-layout"><form id="booking-form"><div class="form-error" role="alert"></div>' + body + '<div class="form-actions">' + (booking.step > 1 ? '<button class="button secondary" type="button" data-action="booking-back">' + icon('arrow-left') + 'Back</button>' : '<a class="button secondary" href="#/trips">Cancel</a>') + '<div class="button-row"><button class="button secondary" type="submit" name="intent" value="draft">Save draft</button><button class="button" type="submit" name="intent" value="' + (booking.step === 3 ? 'confirm' : 'next') + '">' + (booking.step === 3 ? 'Confirm demo booking' : 'Continue') + icon('arrow-right') + '</button></div></div></form>' + bookingSummary() + '</div>');
  }
  function driverOptions() {
    const merchant = merchants.find(m => String(m.id) === booking.merchant);
    if (!merchant) return '<p class="review-note">A merchant and driver can be assigned after saving your booking.</p>';
    return '<label class="driver-choice"><input type="radio" name="driver" value="" ' + (!booking.driver ? 'checked' : '') + '><div><strong>Choose a driver later</strong></div></label>' + merchant.drivers.map(d => '<label class="driver-choice"><input type="radio" name="driver" value="' + d.id + '" ' + (booking.driver === String(d.id) ? 'checked' : '') + '><span class="avatar">' + icon('truck') + '</span><div><strong>' + e(d.full_name) + '</strong><p>' + e(d.vehicle_id) + '</p></div></label>').join('');
  }
  function captureBooking() {
    const form = document.getElementById('booking-form');
    if (!form) return;
    const data = new FormData(form);
    for (const key of ['pickup_location','pickup_datetime','drop_off_location','cost','merchant','driver']) if (data.has(key)) booking[key] = data.get(key);
  }
  async function submitBooking(event) {
    const form = event.target, intent = event.submitter.value;
    captureBooking();
    if (intent === 'next') { booking.step++; bookingPage(); return; }
    const payload = { pickup_location:booking.pickup_location, pickup_datetime:booking.pickup_datetime, drop_off_location:booking.drop_off_location, cost:booking.cost };
    if (booking.driver) payload.driver = booking.driver;
    await perform(form, async () => {
      if (local && intent === 'confirm' && !booking.driver) throw new Error('Choose a driver before confirming, or save this booking as a draft.');
      const draft = await request('POST',booking.id ? '/draft/trips/' + booking.id : '/draft/trips/create',payload);
      booking.id = draft.id;
      if (intent === 'confirm') {
        const trip = await request('POST','/draft/trips/' + draft.id + '/confirm');
        booking = null; go('trip/' + trip.id); toast(local ? 'Booking confirmed locally. No driver notification sent.' : 'Demo booking confirmed. No driver notification sent.');
      } else { booking = null; go('drafts'); toast(local ? 'Draft saved.' : 'Draft saved in this demo.'); }
    });
  }
  function detailPage(trip) {
    const t = trip;
    shell('Trip #' + t.id,heading('Trip #' + t.id,shortDate(t.pick_up_datetime) + ' at ' + time(t.pick_up_datetime),'<div class="button-row">' + linkButton('trips','All trips','arrow-left',true) + (eligible(t) ? '<button class="button" data-pay="' + t.id + '">' + icon('credit-card') + 'Pay for trip</button>' : '') + '</div>') + '<div class="detail-layout"><div><section class="detail-section"><div class="section-title"><h2>Trip details</h2>' + badge(t.status) + '</div><div class="route-point">' + icon('circle') + '<div><small>PICKUP</small><strong>' + e(t.pickup_location_alias || t.pickup_location) + '</strong><p class="review-note">' + e(t.pickup_location) + '</p></div></div><div class="route-point">' + icon('map-pin') + '<div><small>DROP-OFF</small><strong>' + e(t.drop_location) + '</strong></div></div>' + row('Scheduled pickup',shortDate(t.pick_up_datetime) + ', ' + time(t.pick_up_datetime)) + row('Customer',profile.first_name + ' ' + profile.last_name) + '</section><section class="detail-section"><div class="section-title"><h2>Merchant & driver</h2>' + (t.status === 'New' ? '<button class="text-button" data-assign="' + t.id + '">' + icon('edit-2') + 'Change assignment</button>' : '') + '</div>' + row('Merchant',t.merchant) + row('Driver',t.driver) + row('Vehicle',t.truck_number) + '</section></div><aside class="booking-summary"><div class="section-title"><h3>Payment summary</h3>' + badge(t.payment_id ? 'Paid' : 'Unpaid') + '</div>' + row('Trip cost',money(t.total_cost)) + row('Material cost',money(t.cost_of_sand)) + row('Road money',money(t.road_money)) + '<div class="detail-row total"><span>Total</span><strong>' + money(api.total(t)) + '</strong></div>' + (t.payment ? row('Reference',t.payment.transaction_id) + row('Payment method',t.payment.payment_type) : '') + '</aside></div>');
  }
  function profilePage() {
    shell('My account',heading('My account','Your contact and booking details.') + '<div class="profile-layout"><aside class="profile-aside"><div class="avatar">' + e(initials(profile)) + '</div><h3>' + e(profile.first_name + ' ' + profile.last_name) + '</h3><p class="muted">' + e(profile.email) + '</p><span class="demo-tag">Sample account</span></aside><form id="profile-form"><div class="form-error" role="alert"></div><div class="form-grid">' + profileForm() + '</div><div class="form-actions"><button type="button" class="button secondary" data-action="profile-discard">Discard changes</button><button class="button" type="submit">' + icon('check') + 'Save changes</button></div></form></div>');
  }
  function profileForm() { return field('First name','first_name','text',profile.first_name,'required autocomplete="given-name"') + field('Last name','last_name','text',profile.last_name,'autocomplete="family-name"') + field('Email address','email','email',profile.email,'required autocomplete="email"',true) + field('Country code','country_code','text',profile.country_code,'required pattern="\\+[0-9]{1,4}"') + field('Phone number','phone_number','tel',profile.phone_number,'required pattern="[0-9]{9,13}" autocomplete="tel-national"'); }
  function loginPage() {
    const previous = pendingLogin;
    pendingLogin = null;
    const existing = loginMode === 'existing';
    root.innerHTML = '<div class="login-shell"><section class="login-visual" aria-label="FleetNG logistics"><img class="login-photo" src="' + e(loginImageUrl()) + '" alt="Fleet vehicles ready for delivery"><div class="login-visual-content">' + brand() + '<div class="login-visual-message"><span class="login-visual-kicker">LOGISTICS, MADE CLEAR</span><h2>Your freight.<br>Your visibility.</h2><p>Book deliveries and keep every journey in view, from pickup to arrival.</p></div><div class="login-visual-foot"><span>FleetNG customer portal</span><span>Built for the journey ahead</span></div></div></section><section class="login-panel"><div class="login-panel-top"><span class="login-panel-mark">CUSTOMER PORTAL <span aria-hidden="true">/</span> ACCOUNT ACCESS</span><a class="login-home" href="' + e(homeUrl()) + '">' + icon('arrow-up-right') + ' Back to website</a></div><main id="main" class="login-center"><div class="login-form-head"><span class="eyebrow">Customer portal</span><h1>' + (existing ? 'Welcome to FleetNG' : 'Create a demo account') + '</h1><p>' + (existing ? 'Access your bookings, payments, and deliveries in one place.' : 'Start booking and tracking deliveries with FleetNG.') + '</p></div><div class="tabs" role="tablist" aria-label="Account type"><button class="tab" role="tab" data-login="existing" aria-selected="' + existing + '">Existing customer</button><button class="tab" role="tab" data-login="new" aria-selected="' + !existing + '">New customer</button></div><form id="login-form"><div class="form-error" role="alert"></div>' + (existing ? field('Phone number','phone_number','tel',profile ? profile.phone_number : '8000000000','required pattern="[0-9]{9,13}" autocomplete="tel-national" inputmode="tel" placeholder="Enter your phone number"') : field('First name','first_name','text','','required autocomplete="given-name"') + field('Last name','last_name','text','','autocomplete="family-name"') + field('Email address','email','email','','required autocomplete="email"') + field('Country code','country_code','text','+234','required pattern="\\+[0-9]{1,4}"') + field('Phone number','phone_number','tel','','required pattern="[0-9]{9,13}" autocomplete="tel-national"')) + '<button class="button" type="submit"><span>' + (existing ? 'Open demo account' : 'Create demo account') + '</span>' + icon('arrow-right') + '</button></form><p class="review-note">Sample account access only. No OTP is sent.</p><button class="text-button" data-action="reset">' + icon('rotate-ccw') + 'Restore sample account</button></main><div class="login-panel-foot"><span>FLEETNG</span><span>Move with confidence.</span></div></section></div>';
    if (local) {
      root.querySelector('[data-action="reset"]').remove();
      root.querySelector('#login-form button[type="submit"] span').textContent = 'Continue';
      if (!existing) root.querySelector('h1').textContent = 'Create your account';
      if (existing && !profile) root.querySelector('[name="phone_number"]').value = '';
    }
    root.querySelector('.review-note').textContent = verificationNote();
    if (previous && previous.mode === loginMode) {
      root.querySelectorAll('#login-form input').forEach(input => {
        if (Object.prototype.hasOwnProperty.call(previous.data,input.name)) input.value = previous.data[input.name];
      });
    }
    icons();
  }
  function otpPage() {
    const form = document.getElementById('login-form');
    form.id = 'otp-form';
    root.querySelector('.login-form-head .eyebrow').textContent = 'One more step';
    root.querySelector('.login-form-head h1').textContent = 'Verify your number';
    root.querySelector('.login-form-head p').textContent = 'Enter the code for ' + pendingLogin.data.phone_number + ' to continue.';
    root.querySelector('.login-center .tabs').hidden = true;
    form.innerHTML = '<div class="form-error" role="alert"></div>' + field('Verification code','code','text','','required pattern="[0-9]{6}" maxlength="6" inputmode="numeric" autocomplete="one-time-code" aria-describedby="otp-hint" placeholder="6-digit code"') + '<p id="otp-hint" class="review-note">' + (testOtpEnabled() ? 'Testing OTP: <strong>123456</strong>. No SMS was sent.' : 'Enter the verification code sent to this phone number.') + ' Code expires in 5 minutes.</p><button class="button" type="submit"><span>Verify and continue</span>' + icon('arrow-right') + '</button><div class="otp-actions"><button class="text-button" type="button" data-action="otp-restart">Change details</button><button class="text-button" type="button" data-action="otp-resend">Request another code</button></div>';
    form.querySelector('[name="code"]').focus();
    root.querySelector('.login-center > .review-note').hidden = true;
  }
  function openDialog(title, body, actions = '') {
    dialog.innerHTML = '<div class="dialog-head"><h2 id="dialog-title">' + e(title) + '</h2><button class="icon-button" data-close aria-label="Close dialog">' + icon('x') + '</button></div><div class="dialog-body">' + body + '</div>' + (actions ? '<div class="dialog-actions">' + actions + '</div>' : '');
    dialog.showModal(); icons();
  }
  function paymentDialog(ids) {
    if (local) {
      const items = trips.filter(t => ids.includes(t.id) && eligible(t));
      if (!items.length) return;
      openDialog('Review payment',items.map(t => row('Trip #' + t.id,money(api.total(t)))).join('') + '<div class="form-error" role="alert"></div>','<button class="button secondary" data-close>Cancel</button><button class="button" id="open-checkout">Continue to checkout</button>');
      document.getElementById('open-checkout').onclick = async event => {
        const button = event.currentTarget; button.disabled = true;
        try {
          const checkout = await request('POST','/trips/payment-link',{trip_ids:items.map(t => t.id)});
          const url = new URL(checkout.link);
          if (url.protocol !== 'https:' || !(url.hostname === 'flutterwave.com' || url.hostname.endsWith('.flutterwave.com'))) throw new Error('The payment provider returned an invalid link.');
          location.assign(url.href);
        } catch (error) { dialog.querySelector('.form-error').textContent = error.message; button.disabled = false; }
      };
      return;
    }
    const items = trips.filter(t => ids.includes(t.id) && eligible(t));
    if (!items.length) return;
    openDialog('Review demo payment','<span class="demo-tag">No real payment</span><p class="review-note">This records a sample payment in this browser only.</p>' + items.map(t => row('Trip #' + t.id,money(api.total(t)))).join('') + '<div class="detail-row total"><span>Total</span><strong>' + money(items.reduce((s,t) => s+api.total(t),0)) + '</strong></div><div class="form-error" role="alert"></div>','<button class="button secondary" data-close>Cancel</button><button class="button" id="confirm-payment">Simulate successful payment</button>');
    document.getElementById('confirm-payment').onclick = async event => {
      event.currentTarget.disabled = true;
      try { api.simulatePayment(items.map(t => t.id)); selected.clear(); dialog.close(); await render(); toast('Sample payment recorded. No money was charged.'); }
      catch (error) { dialog.querySelector('.form-error').textContent = error.message; event.currentTarget.disabled = false; }
    };
  }
  function assignmentDialog(id) {
    const t = trips.find(t => t.id === id);
    openDialog('Assign a driver','<form id="assignment-form" data-trip="' + id + '"><div class="form-error" role="alert"></div><div class="field"><label for="assignment-driver">Merchant / driver</label><select id="assignment-driver" name="driver" required><option value="">Choose a driver</option>' + merchants.map(m => '<optgroup label="' + e(m.name) + '">' + m.drivers.map(d => '<option value="' + d.id + '" ' + (t.driver_id === d.id ? 'selected' : '') + '>' + e(d.full_name + ' / ' + d.vehicle_id) + '</option>').join('') + '</optgroup>').join('') + '</select></div><div class="form-actions"><button class="button secondary" type="button" data-close>Cancel</button><button class="button" type="submit">Save assignment</button></div></form>');
  }
  async function perform(form, action) {
    const errorNode = form.querySelector('.form-error'); errorNode.textContent = '';
    const buttons = [...form.querySelectorAll('button[type="submit"]')]; buttons.forEach(b => b.disabled = true);
    try { await action(); } catch (error) { errorNode.textContent = error.message; }
    finally { buttons.forEach(b => b.disabled = false); }
  }
  async function render() {
    const version = ++renderId, next = location.hash.replace(/^#\/?/,'') || 'overview';
    if (next !== route) { tableState = { ...tableState, search:'',status:'All',payment:'All',page:1 }; selected.clear(); }
    route = next;
    if (!api.isSignedIn() || route === 'login') {
      if (route.startsWith('payment-return')) returnRoute = route;
      loginPage(); return;
    }
    try {
      [profile,trips,drafts,payments,locations,merchants] = await Promise.all(['/profile','/trips','/draft/trips','/trip-payments','/pickup-locations','/merchants'].map(p => request('GET',p)));
      if (version !== renderId) return;
      document.title = 'Customer Portal | ' + (portalConfig().titleSuffix || 'FleetNG');
      if (route === 'overview') overview();
      else if (route.startsWith('payment-return')) {
        const params = new URLSearchParams(route.split('?')[1] || '');
        let status = 'unverified';
        if (params.get('reference')) {
          try { status = (await request('GET','/checkouts/' + encodeURIComponent(params.get('reference')))).status; } catch (_) { status = 'unverified'; }
        }
        const states = {successful:['Payment received','Your payment has been verified.'],pending:['Payment pending','Your payment is not yet confirmed. Check your payment history before trying again.'],failed:['Payment unsuccessful','No successful payment has been recorded for this checkout.'],unverified:['Payment not verified','We could not verify this payment. Check your payment history before retrying.']};
        const message = states[status] || states.unverified;
        shell('Payment status',heading(message[0],message[1]) + '<div class="button-row">' + linkButton('payments','Payment history','credit-card',true) + '<button class="button" data-action="refresh-payment">Check again</button></div>');
      }
      else if (['trips','drafts','payments'].includes(route)) tablePage();
      else if (route === 'profile') profilePage();
      else if (route === 'book' || /^draft\/\d+$/.test(route)) {
        const id = route.startsWith('draft/') ? Number(route.split('/')[1]) : null;
        const draft = id ? (local ? await request('GET','/draft/trips/' + id) : drafts.find(t => t.id === id)) : null;
        if (id && !draft) { shell('Not found',empty('Draft not found','This draft may already have been confirmed.',linkButton('drafts','All drafts','arrow-left',true))); return; }
        if (!booking || booking.id !== id) booking = newBooking(draft);
        bookingPage();
      } else if (/^trip\/\d+$/.test(route)) detailPage(await request('GET','/trips/' + route.split('/')[1]));
      else shell('Not found',empty('Page not found','Return to your account overview.',linkButton('overview','Overview','arrow-left',true)));
    } catch (error) { root.innerHTML = '<main id="main">' + empty('Unable to load this page',error.message,linkButton('overview','Back to overview','arrow-left',true)) + '</main>'; icons(); }
  }
  document.addEventListener('click', async event => {
    const target = event.target.closest('button,[data-pay],[data-assign]'); if (!target) return;
    if (target.hasAttribute('data-close')) { dialog.close(); return; }
    if (target.dataset.pay) { paymentDialog([Number(target.dataset.pay)]); return; }
    if (target.dataset.assign) { assignmentDialog(Number(target.dataset.assign)); return; }
    if (target.dataset.login) { loginMode = target.dataset.login; loginPage(); return; }
    if (target.dataset.status) { tableState.status = target.dataset.status; tableState.page = 1; selected.clear(); renderTable(); return; }
    if (target.dataset.sort) { tableState.direction = tableState.sort === target.dataset.sort && tableState.direction === 'desc' ? 'asc' : 'desc'; tableState.sort = target.dataset.sort; tableState.page = 1; renderTable(); return; }
    if (target.dataset.page) {
      const pages = Math.max(1,Math.ceil(tableData().length/tableState.size));
      tableState.page = { first:1, previous:Math.max(1,tableState.page-1), next:Math.min(pages,tableState.page+1), last:pages }[target.dataset.page]; renderTable(); return;
    }
    switch (target.dataset.action) {
      case 'nav': document.body.classList.toggle('nav-open'); target.setAttribute('aria-expanded',document.body.classList.contains('nav-open')); break;
      case 'close-nav': document.body.classList.remove('nav-open'); document.querySelector('[data-action="nav"]').setAttribute('aria-expanded','false'); break;
      case 'logout': try { await api.logout(); profile = null; booking = null; selected.clear(); go('login'); } catch (error) { toast(error.message); } break;
      case 'otp-restart': loginPage(); break;
      case 'otp-resend':
        target.disabled = true;
        try {
          const challenge = await request('POST','/otp/request',{phone_number:pendingLogin.data.phone_number});
          pendingLogin.challenge = challenge.challenge_id;
          document.querySelector('#otp-form .form-error').textContent = '';
          document.querySelector('#otp-form [name="code"]').value = '';
          toast(testOtpEnabled() ? 'New testing code ready: 123456. No SMS was sent.' : 'A new verification code has been requested.');
        } catch (error) { toast(error.message); }
        finally { target.disabled = false; }
        break;
      case 'reset': openDialog('Reset sample data?','<p class="muted">Your demo bookings and profile changes will be replaced with the original sample account.</p>','<button class="button secondary" data-close>Keep changes</button><button class="button" id="confirm-reset">Reset demo</button>'); document.getElementById('confirm-reset').onclick = async () => { api.reset(); booking = null; selected.clear(); profile = null; dialog.close(); if (route === 'overview') await render(); else go('overview'); toast('Sample account restored.'); }; break;
      case 'density': tableState.compact = !tableState.compact; renderTable(); break;
      case 'clear-selection': selected.clear(); renderTable(); break;
      case 'pay-selected': paymentDialog([...selected]); break;
      case 'booking-back': captureBooking(); booking.step--; bookingPage(); break;
      case 'profile-discard': profilePage(); break;
      case 'refresh-payment': await render(); break;
    }
  });
  document.addEventListener('keydown', event => { if (event.key === 'Escape') { document.body.classList.remove('nav-open'); const toggle = document.querySelector('[data-action="nav"]'); if (toggle) toggle.setAttribute('aria-expanded','false'); } });
  document.addEventListener('input', event => {
    if (event.target.closest('#booking-form') && booking && booking.step === 1) {
      captureBooking(); document.querySelector('.booking-summary').outerHTML = bookingSummary(); icons();
    }
    if (event.target.id === 'table-search') {
      const cursor = event.target.selectionStart; tableState.search = event.target.value; tableState.page = 1; selected.clear(); renderTable();
      const input = document.getElementById('table-search'); input.focus(); input.setSelectionRange(cursor,cursor);
    }
  });
  document.addEventListener('change', event => {
    const target = event.target;
    if (target.id === 'profile_image') {
      const photo = target.files[0];
      target.setCustomValidity(photo && (!['image/jpeg','image/png','image/webp'].includes(photo.type) || photo.size > 2097152) ? 'Choose a JPEG, PNG or WebP photo smaller than 2 MB.' : '');
      if (!target.checkValidity()) { target.reportValidity(); return; }
      if (photo) {
        const reader = new FileReader();
        reader.onload = () => { const avatar = root.querySelector('.profile-aside .avatar'); if (avatar) avatar.innerHTML = '<img class="profile-photo" src="' + e(reader.result) + '" alt="Selected profile photo">'; };
        reader.readAsDataURL(photo);
      }
    }
    if (target.id === 'merchant') { booking.merchant = target.value; booking.driver = ''; document.getElementById('driver-options').innerHTML = driverOptions(); icons(); }
    if (target.name === 'driver' && document.getElementById('booking-form')) booking.driver = target.value;
    if (target.id === 'payment-filter') { tableState.payment = target.value; tableState.page = 1; selected.clear(); renderTable(); }
    if (target.id === 'page-size') { tableState.size = Number(target.value); tableState.page = 1; renderTable(); }
    if (target.dataset.select) { const id = Number(target.dataset.select); if (target.checked) selected.add(id); else selected.delete(id); renderTable(); }
    if (target.id === 'select-page') { const checked = target.checked; tableData().slice((tableState.page-1)*tableState.size,tableState.page*tableState.size).filter(eligible).forEach(t => { if (checked) selected.add(t.id); else selected.delete(t.id); }); renderTable(); }
  });
  document.addEventListener('submit', async event => {
    event.preventDefault(); const form = event.target;
    if (form.id === 'booking-form') { await submitBooking(event); return; }
    const data = form.id === 'profile-form' ? new FormData(form) : Object.fromEntries(new FormData(form));
    if (data instanceof FormData && !data.get('profile_image')?.size) data.delete('profile_image');
    if (form.id === 'profile-form') await perform(form, async () => { profile = await request('POST','/profile',data); await render(); toast(local ? 'Profile updated.' : 'Demo profile updated.'); });
    if (form.id === 'login-form') await perform(form, async () => {
      const challenge = await request('POST','/otp/request',{phone_number:data.phone_number});
      pendingLogin = {data, mode:loginMode, challenge:challenge.challenge_id};
      otpPage();
    });
    else if (form.id === 'otp-form') await perform(form, async () => {
      const verified = await request('POST','/otp/verify',{challenge_id:pendingLogin.challenge,code:data.code});
      try {
        profile = await request('POST',pendingLogin.mode === 'existing' ? '/login' : '/register',{...pendingLogin.data,verification_token:verified.verification_token});
        pendingLogin = null; booking = null;
        const destination = returnRoute || 'overview'; returnRoute = null;
        go(destination); await render();
      } catch (error) {
        loginPage();
        document.querySelector('.form-error').textContent = error.message + ' Please check your details and request another code.';
      }
    });
    if (form.id === 'assignment-form') await perform(form, async () => { await request('PATCH','/trips/' + form.dataset.trip + '/driver',data); dialog.close(); await render(); toast(local ? 'Driver assignment updated.' : 'Demo driver assignment updated.'); });
  });
  window.addEventListener('hashchange', () => { if (dialog.open) dialog.close(); render(); });
  Promise.resolve(api.ready).then(render);
}());
