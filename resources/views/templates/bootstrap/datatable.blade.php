<div>
    <style>
        .blur {
            filter: blur(2px);
            pointer-events: none;
            user-select: none;
        }
    </style>
    <div data-class="container"
        class="@if ($filter) row @else {{ $this->getClass('container') }} @endif">
        @if ($filter)
            <div wire:transition @class(['col-lg-3', 'pe-0'])>
                <div data-class="filter-panel" @class([$this->getClass('filter_panel'), 'h-100'])
                    style="border-top-right-radius: 0 !important;">
                    <div data-class="filter_header" @class([$this->getClass('filter_header')])
                        style="border-top-right-radius: 0 !important;">
                        <div @class(['d-flex', 'justify-content-between', 'align-items-center'])>
                            <h6 data-class="filter_header_title" @class([$this->getClass('filter_header_title')])>
                                <i @class(['bi', 'bi-funnel', 'me-2'])></i>Filters
                            </h6>
                            <button type="button" wire:click="closeFilter" data-class="filter_close_button"
                                @class([$this->getClass('filter_close_button')]) aria-label="Close"></button>
                        </div>
                    </div>
                    <div data-class="filter_content" @class([$this->getClass('filter_content')])>
                        <div data-class="filter_items" @class([$this->getClass('filter_items')])>
                            @foreach ($filterBy as $key => $item)
                                <div data-class="filter_item" @class([$this->getClass('filter_item'), 'row g-2 align-items-center'])>
                                    <div class="{{ $loop->first ? 'col-12' : 'col-11' }}">
                                        <div data-class="filter_input_group" @class([$this->getClass('filter_input_group')])>
                                            <select data-class="filter_select" @class([$this->getClass('filter_select'), 'pe-4'])
                                                wire:model="filterBy.{{ $key }}">
                                                <option disabled value="">Select field</option>
                                                @foreach ($this->filterByColumn as $colKey => $colLabel)
                                                    <option value="{{ $colKey }}">{{ $colLabel }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" data-class="filter_input"
                                                @class([$this->getClass('filter_input')]) wire:model="query.{{ $key }}"
                                                placeholder="Value...">
                                        </div>
                                    </div>
                                    @if (!$loop->first)
                                        <div @class(['col-1', 'mt-0'])>
                                            <button type="button" wire:click="deleteFilter({{ $loop->index }})"
                                                data-class="filter_delete_button" @class([$this->getClass('filter_delete_button')])
                                                title="Delete Filter">
                                                <i @class(['bi', 'bi-trash'])></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <hr @class(['my-3'])>
                        <div data-class="filter_actions" @class([$this->getClass('filter_actions')])>
                            <button type="button" wire:click="addFilter" wire:loading.attr="disabled"
                                data-class="filter_add_button" {{ $disabledAddFilterButton ? 'disabled' : '' }}
                                @class([$this->getClass('filter_add_button')])>
                                <i wire:loading.remove wire:target="addFilter" @class(['bi', 'bi-plus', 'me-1'])></i>
                                <span wire:loading.remove wire:target="addFilter">Filter</span>
                                <span wire:loading wire:target="addFilter">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                </span>
                            </button>
                            <button type="button" wire:click="resetFilter" data-class="filter_reset_button"
                                @class([$this->getClass('filter_reset_button')])>
                                <i wire:loading.remove wire:target="resetFilter" @class(['bi', 'bi-arrow-clockwise', 'me-1'])></i>
                                <span wire:loading.remove wire:target="resetFilter">Reset</span>
                                <span wire:loading wire:target="resetFilter">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                </span>
                            </button>
                            <button type="button" wire:click="filterData" data-class="filter_apply_button"
                                @class([$this->getClass('filter_apply_button')])>
                                <i wire:loading.remove wire:target="filterData" @class(['bi', 'bi-funnel', 'me-1'])></i>
                                <span wire:loading.remove wire:target="filterData">Filter</span>
                                <span wire:loading wire:target="filterData">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div wire:transition data-class="card" @class([$filter ? 'col-lg-9 px-0' : '', $this->getClass('card')])
            style="{{ $filter ? 'border-top-left-radius: 0 !important;' : '' }}">
            <!-- Header with Controls -->
            <div data-class="card_header" @class([$this->getClass('card_header')])>
                <div data-class="header_row" @class([$this->getClass('header_row')])>
                    <!-- Per Page -->
                    <div data-class="search_col" @class([$this->getClass('search_col')])>
                        <div @class(['d-flex', 'align-items-center', 'gap-2'])>
                            <select id="perPage" name="perPage" wire:model.live="perPage" data-class="per_page_select"
                                @class([$this->getClass('per_page_select')])>
                                @foreach ($pageOptions as $option)
                                    <option value="{{ $option }}">
                                        {{ $option === 'all' ? 'All' : $option }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="perPage" @class(['mb-0', 'text-nowrap', 'text-muted'])>Per Page</label>
                        </div>
                    </div>

                    <!-- Controls Right Side -->
                    <div data-class="controls_col" @class([$this->getClass('controls_col')])>
                        <div data-class="controls_flex" @class([$this->getClass('controls_flex')])>
                            <!-- Filter Button -->
                            @if ($model !== null && $showFiterButton)
                                <button type="button" wire:click="showFilter" data-class="filter_button"
                                    @class([$this->getClass('filter_button')]) title="Open advanced filters">
                                    <i class="bi bi-funnel"></i>
                                </button>
                            @endif

                            <!-- Export Dropdown -->
                            @if ($enableExport && in_array(config('livewire-datatable.export.dropdown.position', 'top'), ['top', 'both']))
                                <div data-class="export_dropdown" @class([$this->getClass('export_dropdown')])>
                                    <button data-class="export_button" @class([$this->getClass('export_button')]) type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i @class(['bi', 'bi-download'])></i>
                                        <span @class(['d-none', 'd-sm-inline', 'ms-1'])>Export</span>
                                    </button>
                                    <ul data-class="export_menu" @class([$this->getClass('export_menu')])>
                                        @foreach ($exportTypes as $type)
                                            <li>
                                                <button wire:click="export('{{ $type }}')" type="button"
                                                    data-class="export_item" @class([$this->getClass('export_item')])>
                                                    @if ($type === 'excel')
                                                        <i @class(['bi', 'bi-file-earmark-spreadsheet', 'me-2'])></i>Excel
                                                    @elseif ($type === 'pdf')
                                                        <i @class(['bi', 'bi-file-earmark-pdf', 'me-2'])></i>PDF
                                                    @else
                                                        <i class="bi bi-download me-2"></i>{{ ucfirst($type) }}
                                                    @endif
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Search -->
                            <div>
                                <div data-class="search_input_group" @class([$this->getClass('search_input_group')])>
                                    <span data-class="search_icon" @class([$this->getClass('search_icon')])>
                                        <i @class(['bi', 'bi-search', 'text-secondary'])></i>
                                    </span>
                                    <input type="search" data-class="search_input" @class([$this->getClass('search_input')])
                                        wire:model.live.debounce.300ms="search" placeholder="Search records..."
                                        {{ $filterDataSearch ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div data-class="table_responsive" @class([$this->getClass('table_responsive')])>
                <table data-class="table" @class([$this->getClass('table')]) style="{{ $this->getClass('table_style') }}">
                    <thead data-class="thead" @class([$this->getClass('thead')])>
                        <tr data-class="thead_row" @class([$this->getClass('thead_row')])>
                            @foreach ($columns as $key => $column)
                                <th scope="col" data-class="th" @class([$this->getClass('th')])>
                                    @if (in_array($key, $sortable))
                                        <button data-class="th_button" @class([$this->getClass('th_button')])
                                            wire:click="sortBy('{{ $key }}')"
                                            wire:key="sort-{{ $key }}"
                                            style="{{ $this->getClass('th_button_style') }}">
                                            {{ $column }}
                                            <span data-class="th_sort_icon" @class([$this->getClass('th_sort_icon')])>
                                                @if ($sortField === $key)
                                                    @if ($sortDirection === 'asc')
                                                        <i data-class="sort_icon_asc" @class([$this->getClass('sort_icon_asc')])
                                                            style="{{ $this->getClass('sort_icon_color_active') }}"></i>
                                                    @else
                                                        <i data-class="sort_icon_desc" @class([$this->getClass('sort_icon_desc')])
                                                            style="{{ $this->getClass('sort_icon_color_active') }}"></i>
                                                    @endif
                                                @else
                                                    <i data-class="sort_icon_neutral" @class([
                                                        $this->getClass('sort_icon_neutral'),
                                                        $this->getClass('sort_icon_inactive_class'),
                                                    ])
                                                        style="{{ $this->getClass('sort_icon_color_inactive') }}"></i>
                                                @endif
                                            </span>
                                        </button>
                                    @else
                                        <span @class(['text-secondary'])>{{ $column }}</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody wire:loading.class="blur" data-class="tbody" @class([$this->getClass('tbody')])>
                        @forelse($this->getQuery as $index => $item)
                            <tr wire:key="row-{{ $item->id ?? $index }}" id="row-{{ $item->id ?? $index }}"
                                data-class="tr" @class([$this->getClass('tr')])>
                                @foreach ($columns as $key => $column)
                                    <td data-class="td" @class([$this->getClass('td')])
                                        wire:key="cell-{{ $key }}">
                                        <div data-class="td_{{ $key }}" @class([$this->getClass("td_{$key}")])>
                                            @if (isset($customColumns[$key]))
                                                @include($customColumns[$key], [
                                                    'item' => $item,
                                                    'value' => data_get($item, $key),
                                                ])
                                            @else
                                                @if ($key === 'no')
                                                    @if ($sortField === 'no')
                                                        @if ($sortDirection === 'desc')
                                                            @if (config('livewire-datatable.default_pagination') == 'simplePaginate')
                                                                {{ $this->totals - (($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() + $loop->parent->iteration) + 1 }}
                                                            @else
                                                                {{ $this->getQuery->total() - (($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() + $loop->parent->iteration) + 1 }}
                                                            @endif
                                                        @else
                                                            {{ $loop->parent->iteration + ($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() }}
                                                        @endif
                                                    @else
                                                        {{ $loop->parent->iteration + ($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() }}
                                                    @endif
                                                @else
                                                    {!! $this->formatValue($key, data_get($item, $key)) !!}
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) }}" data-class="empty_wrapper"
                                    @class([$this->getClass('empty_wrapper')])>
                                    <div data-class="empty_content" @class([$this->getClass('empty_content')])>
                                        <i data-class="empty_icon" @class([$this->getClass('empty_icon')])
                                            style="{{ $this->getClass('empty_icon_style') }}"></i>
                                        <p @class(['mt-3', 'mb-0'])>
                                            <small>No records found</small>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($this->getQuery->hasPages())
                <nav data-class="card_footer" @class([$this->getClass('card_footer')]) aria-label="Page navigation">
                    <div data-class="pagination_wrapper" @class([$this->getClass('pagination_wrapper')])>
                        <small data-class="pagination_info" @class([$this->getClass('pagination_info')])>
                            Showing
                            <strong>{{ ($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() + 1 }}</strong>
                            to
                            <strong>{{ min($this->getQuery->currentPage() * $this->getQuery->perPage(), $this->getQuery->total()) }}</strong>
                            of
                            <strong>{{ $this->getQuery->total() }}</strong>
                            records
                        </small>
                        <div data-class="pagination_controls" @class([$this->getClass('pagination_controls')])>
                            {{ $this->getQuery->links() }}
                        </div>
                    </div>
                </nav>
            @endif
        </div>
    </div>
</div>
