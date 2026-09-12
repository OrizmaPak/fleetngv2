(function () {
  'use strict';
  const key = 'fleetng-customer-preview-v1';
  const clone = value => JSON.parse(JSON.stringify(value));
  const date = (offset, hour = 9) => {
    const d = new Date();
    d.setDate(d.getDate() + offset);
    d.setHours(hour, 0, 0, 0);
    return d.toISOString();
  };
  const locations = [
    { id: 1, location_name: 'Eleme loading yard', location: 'Eleme, Port Harcourt' },
    { id: 2, location_name: 'Onne depot', location: 'Onne, Rivers State' },
    { id: 3, location_name: 'Trans Amadi yard', location: 'Trans Amadi, Port Harcourt' }
  ];
  const merchants = [
    { id: 21, name: 'Rivers Haulage', drivers: [{ id: 101, full_name: 'Demo Driver A', vehicle_id: 'DEMO-101' }, { id: 102, full_name: 'Demo Driver B', vehicle_id: 'DEMO-102' }] },
    { id: 22, name: 'Coastal Logistics', drivers: [{ id: 103, full_name: 'Demo Driver C', vehicle_id: 'DEMO-103' }] }
  ];
  function driverInfo(id) {
    const merchant = merchants.find(m => m.drivers.some(d => d.id === Number(id)));
    const driver = merchant && merchant.drivers.find(d => d.id === Number(id));
    return { driver_id: driver ? driver.id : null, driver: driver ? driver.full_name : null, merchant_id: merchant ? merchant.id : null, merchant: merchant ? merchant.name : null, truck_number: driver ? driver.vehicle_id : null };
  }
  function seed() {
    const destinations = ['Woji construction site', 'GRA Phase 2, Port Harcourt', 'Rumuola, Port Harcourt', 'Choba project site'];
    const statuses = ['Running', 'New', 'Completed', 'Completed', 'Canceled', 'Completed', 'Declined', 'Completed', 'New'];
    const trips = statuses.map((status, i) => ({
      id: 2401 + i, client_id: 1, pickup_location_id: i % 3 + 1,
      pickup_location: locations[i % 3].location, pickup_location_alias: locations[i % 3].location_name,
      drop_location: destinations[i % 4], pick_up_datetime: date(i < 2 ? i : -i),
      total_cost: 85000 + i * 15000, cost_of_sand: i === 2 ? 30000 : 0, road_money: i === 2 ? 5000 : 0,
      status, payment_id: [3, 5, 7].includes(i) ? 701 + i : null,
      ...driverInfo(i === 1 ? null : 101 + i % 3)
    }));
    return {
      version: 1, signedIn: true,
      profile: { id: 1, first_name: 'Alex', last_name: 'Morgan', email: 'alex@example.com', country_code: '+234', phone_number: '8000000000', profile_image: null },
      trips,
      drafts: [{ ...clone(trips[1]), id: 901, status: 'Draft', payment_id: null, pick_up_datetime: date(3), drop_location: 'Rumuokoro project site' }],
      payments: trips.filter(t => t.payment_id).map(t => ({ id: t.payment_id, trip_id: t.id, customer_id: 1, amount: total(t), status: 'successful', transaction_id: 'DEMO-' + t.payment_id, reference_code: 'PREVIEW-' + t.payment_id, payment_type: 'card', created_at: t.pick_up_datetime }))
    };
  }
  const total = trip => Number(trip.total_cost || 0) + Number(trip.cost_of_sand || 0) + Number(trip.road_money || 0);
  let state;
  try { state = JSON.parse(sessionStorage.getItem(key)); } catch (_) { /* Storage may be unavailable for a file preview. */ }
  if (!state || state.version !== 1) state = seed();
  function save() { try { sessionStorage.setItem(key, JSON.stringify(state)); } catch (_) { /* In-memory preview remains usable. */ } }
  function fail(message, errors = {}) { const error = new Error(message); error.errors = errors; throw error; }
  function requireAuth() { if (!state.signedIn) fail('Your demo session has ended. Sign in again.'); }
  function profileFields(data) {
    if (!String(data.first_name || '').trim()) fail('Enter your first name.', { first_name: 'Required' });
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email || '')) fail('Enter a valid email address.', { email: 'Invalid email' });
    if (!/^\d{9,13}$/.test(data.phone_number || '')) fail('Enter a phone number with 9 to 13 digits.', { phone_number: 'Invalid phone number' });
    return { first_name: data.first_name.trim(), last_name: String(data.last_name || '').trim(), email: data.email.trim(), country_code: data.country_code || '+234', phone_number: data.phone_number };
  }
  function draftFields(data) {
    const location = locations.find(l => l.id === Number(data.pickup_location));
    if (!location) fail('Choose a pickup location.');
    const pickup = new Date(data.pickup_datetime);
    const today = new Date(); today.setHours(0, 0, 0, 0);
    if (!Number.isFinite(pickup.getTime()) || pickup < today) fail('Choose today or a future pickup date.');
    if (!String(data.drop_off_location || '').trim() || data.drop_off_location.length > 255) fail('Enter a drop-off location of up to 255 characters.');
    const cost = Number(data.cost);
    if (!Number.isSafeInteger(cost) || cost < 0) fail('Enter a non-negative whole-naira cost.');
    const driver = driverInfo(data.driver);
    if (data.driver && !driver.driver_id) fail('Choose an available driver.');
    return { pickup_location_id: location.id, pickup_location: location.location, pickup_location_alias: location.location_name, drop_location: data.drop_off_location.trim(), pick_up_datetime: pickup.toISOString(), total_cost: cost, cost_of_sand: 0, road_money: 0, ...driver };
  }
  const enrich = t => ({ ...clone(t), total_trip_cost: total(t), payment: clone(state.payments.find(p => p.id === t.payment_id) || null) });
  const find = (collection, id) => { const item = collection.find(t => t.id === Number(id)); if (!item) fail('This record could not be found.'); return item; };
  // Route-shaped mock transport. There is intentionally no network transport in this preview.
  let otpChallenge = null, otpProof = null;
  async function request(method, path, data = {}) {
    if (data instanceof FormData) {
      data = Object.fromEntries(data);
      const photo = data.profile_image;
      delete data.profile_image;
      if (photo && photo.size) {
        if (!['image/jpeg','image/png','image/webp'].includes(photo.type) || photo.size > 2097152) fail('Choose a JPEG, PNG or WebP photo smaller than 2 MB.');
        data.profile_image = await new Promise((resolve,reject) => {
          const reader = new FileReader(); reader.onload = () => resolve(reader.result); reader.onerror = () => reject(new Error('Unable to read this photo.')); reader.readAsDataURL(photo);
        });
      }
    }
    let result;
    if (path === '/otp/request' && method === 'POST') {
      if (!/^[0-9]{9,13}$/.test(data.phone_number)) fail('Enter a valid phone number.');
      otpChallenge = {id:crypto.randomUUID(),phone:data.phone_number,expires:Date.now()+300000,attempts:0};
      result = {challenge_id:otpChallenge.id,development_otp:'123456',expires_in:300};
    } else if (path === '/otp/verify' && method === 'POST') {
      if (!otpChallenge || otpChallenge.id !== data.challenge_id || otpChallenge.expires < Date.now() || otpChallenge.attempts >= 5) fail('This code has expired. Request another code.');
      otpChallenge.attempts++;
      if (data.code !== '123456') fail('Incorrect code. The development code is 123456.');
      otpProof = {id:crypto.randomUUID(),phone:otpChallenge.phone,expires:Date.now()+300000};
      otpChallenge = null;
      result = {verification_token:otpProof.id};
    } else if ((path === '/login' || path === '/register') && method === 'POST') {
      if (!otpProof || otpProof.id !== data.verification_token || otpProof.phone !== data.phone_number || otpProof.expires < Date.now()) fail('Verify your phone number first.');
      otpProof = null;
      if (path === '/login') {
      if (data.phone_number !== state.profile.phone_number) fail('Use the sample account phone number, or create a new demo account.');
      state.signedIn = true; result = state.profile;
      } else {
      state = { version: 1, signedIn: true, profile: { id: 1, ...profileFields(data), profile_image: null }, trips: [], drafts: [], payments: [] }; result = state.profile;
      }
    } else if (path === '/pickup-locations' && method === 'GET') result = locations;
    else {
      requireAuth();
      if (path === '/profile' && method === 'GET') result = state.profile;
      else if (path === '/profile' && method === 'POST') { Object.assign(state.profile, profileFields(data)); if (data.profile_image) state.profile.profile_image = data.profile_image; result = state.profile; }
      else if (path === '/merchants' && method === 'GET') result = merchants;
      else if (path === '/stats' && method === 'GET') result = {
        all_trips: state.trips.length, completed_trips: state.trips.filter(t => t.status === 'Completed').length,
        canceled_trips: state.trips.filter(t => t.status === 'Canceled').length, declined_trips: state.trips.filter(t => t.status === 'Declined').length,
        paid_trips: state.trips.filter(t => t.payment_id).length, unpaid_trips: state.trips.filter(t => !t.payment_id).length,
        total_payment: state.trips.reduce((sum, t) => sum + t.total_cost, 0)
      };
      else if (path === '/trips' && method === 'GET') result = state.trips.map(enrich);
      else if (path === '/trip-payments' && method === 'GET') result = state.payments.map(p => ({ ...clone(p), trip: enrich(find(state.trips, p.trip_id)) }));
      else if (path === '/draft/trips' && method === 'GET') result = state.drafts.map(enrich);
      else if (path === '/draft/trips/create' && method === 'POST') {
        const trip = { id: Math.max(900, ...state.drafts.map(t => t.id)) + 1, client_id: state.profile.id, status: 'Draft', payment_id: null, ...draftFields(data) };
        state.drafts.unshift(trip); result = enrich(trip);
      } else if (/^\/draft\/trips\/\d+\/confirm$/.test(path) && method === 'POST') {
        const draft = find(state.drafts, path.split('/')[3]);
        const t = { ...clone(draft), id: Math.max(2400, ...state.trips.map(t => t.id)) + 1, status: 'New' };
        state.trips.unshift(t); state.drafts = state.drafts.filter(d => d.id !== draft.id); result = enrich(t);
      } else if (/^\/draft\/trips\/\d+$/.test(path)) {
        const draft = find(state.drafts, path.split('/')[3]);
        if (method === 'POST') Object.assign(draft, draftFields(data));
        else if (method !== 'GET') fail('Unsupported preview action.');
        result = enrich(draft);
      } else if (/^\/trips\/\d+\/driver$/.test(path) && method === 'PATCH') {
        const t = find(state.trips, path.split('/')[2]);
        const driver = driverInfo(data.driver); if (!driver.driver_id) fail('Choose a driver.');
        Object.assign(t, driver); result = enrich(t);
      } else if (/^\/trips\/\d+$/.test(path) && method === 'GET') result = enrich(find(state.trips, path.split('/')[2]));
      else fail('This action is not available in the preview.');
    }
    save(); return { status: true, message: '', errors: [], data: clone(result) };
  }
  function simulatePayment(ids) {
    requireAuth();
    const trips = [...new Set(ids.map(Number))].map(id => find(state.trips, id));
    if (!trips.length || trips.some(t => t.payment_id || ['Canceled', 'Declined'].includes(t.status))) fail('Select unpaid, eligible trips.');
    for (const t of trips) {
      const id = Math.max(700, ...state.payments.map(p => p.id)) + 1;
      const p = { id, trip_id: t.id, customer_id: state.profile.id, amount: total(t), status: 'successful', transaction_id: 'DEMO-' + id, reference_code: 'PREVIEW-' + id, payment_type: 'card', created_at: new Date().toISOString() };
      state.payments.unshift(p); t.payment_id = id;
    }
    save();
  }
  window.CustomerPreview = { request, simulatePayment, total,
    isSignedIn: () => state.signedIn,
    logout: () => { state.signedIn = false; save(); },
    reset: () => { state = seed(); save(); }
  };
}());
