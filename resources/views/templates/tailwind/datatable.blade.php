<div>
    <div data-class="wrapper" @class([$this->getClass('wrapper')])>

        {{-- ═══════════════════════════════════════════════════ --}}
        {{-- FILTER PANEL                                        --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        @if ($filter)
            <div wire:transition data-class="filter_panel" @class([$this->getClass('filter_panel')])>
                <div data-class="filter_header" @class([$this->getClass('filter_header')])>
                    <p data-class="filter_header_title" @class([$this->getClass('filter_header_title')])>View</p>
                    <button type="button" wire:click="closeFilter" data-class="filter_close_button"
                        @class([$this->getClass('filter_close_button')]) title="close">
                        <svg data-class="filter_close_button_icon" @class([$this->getClass('filter_close_button_icon')])
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="m12 13.4l-4.9 4.9q-.275.275-.7.275t-.7-.275t-.275-.7t.275-.7l4.9-4.9l-4.9-4.9q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l4.9 4.9l4.9-4.9q.275-.275.7-.275t.7.275t.275.7t-.275.7L13.4 12l4.9 4.9q.275.275.275.7t-.275.7t-.7.275t-.7-.275z" />
                        </svg>
                    </button>
                </div>

                <div data-class="filter_content" @class([$this->getClass('filter_content')])>
                    <span data-class="filter_label" @class([$this->getClass('filter_label')])>Filter list by: </span>
                    <div data-class="filter_list" @class([$this->getClass('filter_list')])>
                        <div data-class="filter_items" @class([$this->getClass('filter_items')])>
                            @foreach ($filterBy as $key => $item)
                                <div data-class="filter_item" @class([$this->getClass('filter_item')])>
                                    <div data-class="filter_input_wrapper" @class([$this->getClass('filter_input_wrapper')])>
                                        <input type="text" wire:model="query.{{ $key }}"
                                            data-class="filter_input" @class([$this->getClass('filter_input')])
                                            placeholder="Search...">
                                        <div data-class="filter_select_wrapper" @class([$this->getClass('filter_select_wrapper')])>
                                            <select wire:model="filterBy.{{ $key }}" data-class="filter_select"
                                                @class([$this->getClass('filter_select')])>
                                                <option disabled value="">Choose</option>
                                                @foreach ($this->filterByColumn as $colKey => $colItem)
                                                    <option value="{{ $colKey }}">{{ $colItem }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if (!$loop->first)
                                        <button type="button" wire:click="deleteFilter({{ $loop->index }})"
                                            data-class="filter_delete_button" @class([$this->getClass('filter_delete_button')])
                                            title="remove">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                    d="m12 13.4l-4.9 4.9q-.275.275-.7.275t-.7-.275t-.275-.7t.275-.7l4.9-4.9l-4.9-4.9q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l4.9 4.9l4.9-4.9q.275-.275.7-.275t.7.275t.275.7t-.275.7L13.4 12l4.9 4.9q.275.275.275.7t-.275.7t-.7.275t-.7-.275z" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div data-class="filter_actions" @class([$this->getClass('filter_actions')])>
                        <button type="button" wire:click="addFilter" wire:loading.attr="disabled"
                            data-class="filter_add_button" @class([$this->getClass('filter_add_button')])
                            {{ $disabledAddFilterButton ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2z" />
                            </svg>
                            <span wire:loading.remove wire:target="addFilter">Filter..</span>
                            <span wire:loading wire:target="addFilter">Loading..</span>
                        </button>
                        <button type="button" wire:click="resetFilter" data-class="filter_reset_button"
                            @class([$this->getClass('filter_reset_button')])>Reset</button>
                        <button type="button" wire:click="filterData" data-class="filter_apply_button"
                            @class([$this->getClass('filter_apply_button')])>Filter</button>
                    </div>

                    {{-- #8: Saved Filter Presets --}}
                    @if ($savedFilters)
                        <div class="mt-4 border-t pt-4 border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Saved Presets</p>
                            @foreach ($filterPresets as $preset)
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <button type="button" wire:click="applyFilterPreset('{{ $preset['name'] }}')"
                                        class="text-sm text-blue-600 hover:underline dark:text-blue-400 truncate">
                                        {{ $preset['name'] }}
                                    </button>
                                    <button type="button" wire:click="deleteFilterPreset('{{ $preset['name'] }}')"
                                        class="text-xs text-red-500 hover:text-red-700">✕</button>
                                </div>
                            @endforeach
                            @if ($showSavePreset)
                                <div class="flex gap-2 mt-2">
                                    <input type="text" wire:model="newPresetName" placeholder="Preset name..."
                                        class="text-sm border rounded px-2 py-1 flex-1 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200">
                                    <button type="button" wire:click="saveFilterPreset"
                                        class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                                </div>
                            @else
                                <button type="button" wire:click="$set('showSavePreset', true)"
                                    class="mt-2 text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    + Save current filter as preset
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════ --}}
        {{-- MAIN CONTENT                                        --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div wire:transition data-class="main_wrapper" @class([
            $filter
                ? $this->getClass('main_wrapper_with_filter')
                : $this->getClass('main_wrapper'),
        ])>

            {{-- TOOLBAR --}}
            <div data-class="search_wrapper" @class([$this->getClass('search_wrapper')])>
                <div data-class="controls_layout_top" @class([$this->getClass('controls_layout_top')])>
                    <div data-class="per_page_wrapper" @class([$this->getClass('per_page_wrapper')])>
                        <select name="perPage" wire:model.live="perPage" data-class="per_page_select"
                            @class([$this->getClass('per_page_select')])>
                            @foreach ($pageOptions as $option)
                                <option value="{{ $option }}">{{ $option === 'all' ? 'All' : $option }}</option>
                            @endforeach
                        </select>
                        <span data-class="per_page_text" @class([$this->getClass('per_page_text')])>Per Page</span>
                    </div>
                </div>

                <div data-class="controls_layout_bottom" @class([$this->getClass('controls_layout_bottom')])>

                    {{-- #6: Column Visibility Toggle --}}
                    @if ($columnVisibility)
                        <div class="relative" x-data="{ openCols: false }">
                            <button @click="openCols = !openCols" type="button"
                                class="py-2 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-sm border border-gray-200 text-gray-800 hover:text-blue-500 hover:bg-gray-50 focus:outline-hidden focus:border-gray-500 focus:text-gray-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-700 dark:text-white dark:hover:text-gray-300 dark:hover:border-gray-300 bg-white dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:bg-gray-700"
                                title="Toggle columns">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M3 5h2V3H3zm0 4h2V7H3zm0 4h2v-2H3zm0 4h2v-2H3zm0 4h2v-2H3zm4 0h14v-2H7zm0-4h14v-2H7zm0-4h14v-2H7zm0-4h14V7H7zm0-4v2h14V3H7z" />
                                </svg>
                            </button>
                            <div x-show="openCols" @click.outside="openCols = false" x-transition
                                class="absolute right-0 z-20 mt-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded shadow-lg py-1"
                                style="display:none;">
                                @foreach ($columns as $colKey => $colLabel)
                                    <label
                                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer text-sm text-gray-700 dark:text-gray-200">
                                        <input type="checkbox" wire:click="toggleColumn('{{ $colKey }}')"
                                            {{ $this->isColumnVisible($colKey) ? 'checked' : '' }}>
                                        {{ $colLabel }}
                                    </label>
                                @endforeach
                                <div class="border-t border-gray-100 dark:border-gray-600 mt-1 pt-1 px-3">
                                    <button type="button" wire:click="showAllColumns"
                                        class="text-xs text-blue-600 hover:underline dark:text-blue-400">Show
                                        all</button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- #3 FIX: use $showFilterButton (was $showFiterButton) --}}
                    @if ($model !== null && $showFilterButton)
                        <button type="button" wire:click="showFilter" data-class="filter_button"
                            @class([$this->getClass('filter_button')]) title="filter">
                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M6.532 4.75h6.936c.457 0 .854 0 1.165.03c.307.028.685.095.993.348c.397.326.621.814.624 1.322c.002.39-.172.726-.34.992c-.168.27-.411.59-.695.964l-.031.04l-.01.013l-2.555 3.369c-.252.332-.315.42-.359.51a1.2 1.2 0 0 0-.099.297c-.02.1-.023.212-.023.634v4.243c0 .208 0 .412-.014.578c-.015.164-.052.427-.224.663c-.21.287-.537.473-.9.495c-.302.019-.547-.103-.69-.183c-.144-.08-.309-.195-.476-.31l-.989-.683l-.048-.033c-.191-.131-.403-.276-.562-.477a1.7 1.7 0 0 1-.303-.585c-.071-.244-.07-.5-.07-.738v-2.97c0-.422-.004-.534-.023-.634a1.2 1.2 0 0 0-.1-.297c-.043-.09-.106-.178-.358-.51L4.825 8.459l-.01-.012l-.03-.04c-.284-.375-.527-.695-.696-.965c-.167-.266-.34-.602-.339-.992a1.72 1.72 0 0 1 .624-1.322c.308-.253.686-.32.993-.349c.311-.029.707-.029 1.165-.029" />
                            </svg>
                        </button>
                    @endif

                    @if ($enableExport && in_array(config('livewire-datatable.export.dropdown.position', 'top'), ['top', 'both']))
                        <div data-class="export_dropdown_wrapper" @class([$this->getClass('export_dropdown_wrapper')])
                            x-data="{ open: false }">
                            <button @click="open = !open" @keydown.escape.window="open = false"
                                @click.outside="open = false" type="button"
                                class="{{ config('livewire-datatable.export.dropdown.trigger_class') }}">
                                <span>{!! config('livewire-datatable.export.dropdown.trigger_text', 'Export') !!}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    @class([$this->getClass('export_dropdown_arrow')])>
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition
                                class="{{ config('livewire-datatable.export.dropdown.menu_class') }}"
                                style="display: none;">
                                <div class="py-1" role="menu">
                                    @foreach ($exportTypes as $type)
                                        <button wire:click="export('{{ $type }}')" @click="open = false"
                                            type="button"
                                            class="{{ config('livewire-datatable.export.dropdown.item_class') }}"
                                            role="menuitem">
                                            {!! config('livewire-datatable.export.dropdown.' . $type . '_text', 'Export ' . ucfirst($type)) !!}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- #2: Selection count badge --}}
                    @if ($selectable && count($selectedIds) > 0)
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                {{ count($selectedIds) }} selected
                            </span>
                            <button type="button" wire:click="clearSelection"
                                class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">Clear</button>
                        </div>
                    @endif

                    <div data-class="search_input_wrapper" @class([$this->getClass('search_input_wrapper')])>
                        <label>
                            <span data-class="search_icon_wrapper" @class([$this->getClass('search_icon_wrapper')])>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" data-class="search_icon" @class([$this->getClass('search_icon')])>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="search" name="search" wire:model.live.debounce.300ms="search"
                                placeholder="Search..." data-class="search_input" @class([$this->getClass('search_input')])
                                {{ $filterDataSearch ? 'disabled' : '' }}>
                        </label>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div data-class="table_wrapper" @class([$this->getClass('table_wrapper')])>
                <table data-class="table" @class([$this->getClass('table')])>
                    <thead data-class="thead" @class([$this->getClass('thead')])>
                        <tr data-class="thead_row" @class([$this->getClass('thead_row')])>

                            {{-- #2: Select-all checkbox --}}
                            @if ($selectable)
                                <th scope="col" data-class="th" @class([$this->getClass('th')]) style="width:40px;">
                                    <input type="checkbox" wire:click="toggleSelectAll"
                                        {{ $selectAll ? 'checked' : '' }}
                                        class="rounded border-gray-300 dark:border-gray-600">
                                </th>
                            @endif

                            {{-- #6: Only render visible columns --}}
                            @foreach ($this->activeColumns as $key => $column)
                                <th scope="col" data-class="th" @class([$this->getClass('th')])>
                                    @if (in_array($key, $sortable))
                                        {{-- #9: Multi-sort button (Ctrl+Click adds to stack) --}}
                                        <button
                                            @if ($multiSort) x-on:click="$event.ctrlKey || $event.metaKey
                                                    ? $wire.sortByMulti('{{ $key }}', true)
                                                    : $wire.sortByMulti('{{ $key }}', false)"
                                            @else
                                                wire:click="sortBy('{{ $key }}')" @endif
                                            wire:key="sort-{{ $key }}" data-class="th_sort_button"
                                            @class([$this->getClass('th_sort_button')])>
                                            <span>{{ $column }}</span>
                                            {{-- #9: Priority badge for multi-sort --}}
                                            @if ($multiSort && ($priority = $this->getSortPriority($key)))
                                                <span
                                                    class="inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-blue-500 rounded-full ml-1">{{ $priority }}</span>
                                            @endif
                                            <span data-class="th_sort_icon_wrapper" @class([$this->getClass('th_sort_icon_wrapper')])>
                                                @if ($sortField === $key)
                                                    @if ($sortDirection === 'asc')
                                                        <svg @class([$this->getClass('th_sort_icon_active')])
                                                            xmlns="http://www.w3.org/2000/svg" width="96"
                                                            height="96" viewBox="0 0 24 24">
                                                            <path fill="none" stroke="currentColor"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="m3 8l4-4l4 4M7 4v16m4-8h4m-4 4h7m-7 4h10" />
                                                        </svg>
                                                    @else
                                                        <svg @class([$this->getClass('th_sort_icon_active')])
                                                            xmlns="http://www.w3.org/2000/svg" width="96"
                                                            height="96" viewBox="0 0 24 24">
                                                            <path fill="currentColor" stroke="currentColor"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="m3 16l4 4l4-4m-4 4V4m4 0h10M11 8h7m-7 4h4" />
                                                        </svg>
                                                    @endif
                                                @else
                                                    <svg @class([$this->getClass('th_sort_icon_inactive')])
                                                        xmlns="http://www.w3.org/2000/svg" width="96"
                                                        height="96" viewBox="0 0 24 24">
                                                        <path fill="currentColor" stroke="currentColor"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="m3 16l4 4l4-4m-4 4V4m4 0h10M11 8h7m-7 4h4" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </button>
                                    @else
                                        <span data-class="th_text"
                                            @class([$this->getClass('th_text')])>{{ $column }}</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>

                        {{-- #5: Per-column search row --}}
                        @if ($perColumnSearch)
                            <tr>
                                @if ($selectable)
                                    <th></th>
                                @endif
                                @foreach ($this->activeColumns as $key => $column)
                                    <th class="px-2 py-1">
                                        <input type="search"
                                            wire:model.live.debounce.400ms="columnSearch.{{ str_replace('.', '___', $key) }}"
                                            placeholder="{{ $column }}..."
                                            class="w-full text-xs border border-gray-200 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-400">
                                    </th>
                                @endforeach
                            </tr>
                        @endif
                    </thead>

                    <tbody wire:loading.class="blur" @class([$this->getClass('tbody')])>
                        @forelse($this->getQuery as $index => $item)
                            <tr @class([$this->getClass('tr')]) wire:key="row-{{ $item->id ?? $index }}"
                                id="row-{{ $item->id ?? $index }}">

                                {{-- #2: Row checkbox --}}
                                @if ($selectable)
                                    <td @class([$this->getClass('td')])>
                                        <input type="checkbox" wire:click="toggleSelect({{ $item->id ?? $index }})"
                                            {{ $this->isSelected($item->id ?? $index) ? 'checked' : '' }}
                                            class="rounded border-gray-300 dark:border-gray-600">
                                    </td>
                                @endif

                                @foreach ($this->activeColumns as $key => $column)
                                    <td @class([$this->getClass('td')]) wire:key="cell-{{ $key }}">
                                        <div data-class="td_{{ $key }}" @class([$this->getClass("td_{$key}")])>
                                            @if (isset($customColumns[$key]))
                                                @include($customColumns[$key], [
                                                    'item' => $item,
                                                    'value' => data_get($item, $key),
                                                ])
                                            @else
                                                @if ($key === 'no')
                                                    @if ($sortField === 'no' && $sortDirection === 'desc')
                                                        @if (config('livewire-datatable.default_pagination') == 'simplePaginate')
                                                            {{ $this->totals - (($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() + $loop->parent->iteration) + 1 }}
                                                        @else
                                                            {{ $this->getQuery->total() - (($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() + $loop->parent->iteration) + 1 }}
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
                                <td colspan="{{ count($this->activeColumns) + ($selectable ? 1 : 0) }}"
                                    data-class="empty_wrapper" @class([$this->getClass('empty_wrapper')])>
                                    <div data-class="empty_content" @class([$this->getClass('empty_content')])>
                                        <svg data-class="empty_icon" @class([$this->getClass('empty_icon')])
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <span data-class="empty_text" @class([$this->getClass('empty_text')])>No records
                                            found.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->getQuery->hasPages())
                <div data-class="pagination_wrapper" @class([$this->getClass('pagination_wrapper')])>
                    {{ $this->getQuery->links() }}
                </div>
            @endif

            @if ($enableExport && in_array(config('livewire-datatable.export.dropdown.position', 'top'), ['bottom', 'both']))
                <div class="flex justify-end mt-4" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" type="button"
                        class="{{ config('livewire-datatable.export.dropdown.trigger_class') }}">
                        {!! config('livewire-datatable.export.dropdown.trigger_text', 'Export') !!}
                    </button>
                    <div x-show="open" x-transition
                        class="{{ config('livewire-datatable.export.dropdown.menu_class') }}" style="display:none;">
                        @foreach ($exportTypes as $type)
                            <button wire:click="export('{{ $type }}')" type="button"
                                class="{{ config('livewire-datatable.export.dropdown.item_class') }}">
                                {!! config('livewire-datatable.export.dropdown.' . $type . '_text', 'Export ' . ucfirst($type)) !!}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
