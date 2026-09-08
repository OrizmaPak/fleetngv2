@props([
    'id',
    'endpoint',
    'columns',
    'label' => 'Records',
    'emptyMessage' => 'No records match the current filters.',
])

<div id="{{ $id }}" class="fleetng-data-table" data-fleetng-table
     data-endpoint="{{ $endpoint }}"
     data-columns='@json($columns)'
     data-empty-message="{{ $emptyMessage }}">
    <div class="fleetng-table-toolbar">
        <label class="fleetng-table-search">
            <span class="sr-only">Search {{ strtolower($label) }}</span>
            <i data-feather="search" aria-hidden="true"></i>
            <input type="search" class="form-control" data-table-search placeholder="Search {{ strtolower($label) }}">
        </label>
        <div class="fleetng-density-controls" aria-label="Table density">
            <button type="button" class="btn btn-sm btn-outline-secondary active" data-table-density="cozy">Cozy</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-table-density="compact">Compact</button>
        </div>
    </div>

    <div class="fleetng-table-scroll">
        <table class="table" aria-label="{{ $label }}">
            <thead><tr data-table-head></tr></thead>
            <tbody data-table-body></tbody>
        </table>
        <div class="fleetng-table-state" data-table-state role="status" aria-live="polite">Loading {{ strtolower($label) }}...</div>
    </div>

    <div class="fleetng-table-footer">
        <div data-table-summary aria-live="polite"></div>
        <label class="fleetng-page-size">Rows
            <select class="custom-select" data-table-page-size>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </label>
        <nav class="fleetng-table-pagination" aria-label="{{ $label }} pagination">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-table-page="first" aria-label="First page">&laquo;</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-table-page="previous" aria-label="Previous page">&lsaquo;</button>
            <span data-table-page-label></span>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-table-page="next" aria-label="Next page">&rsaquo;</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-table-page="last" aria-label="Last page">&raquo;</button>
        </nav>
    </div>
</div>
