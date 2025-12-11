<div>
    <div data-class="wrapper" @class([$this->getClass('wrapper')])>
        @if ($filter)
            <div data-class="filter_panel" @class([$this->getClass('filter_panel')])>
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
                                        <input type="text" id="hs-inline-leading-select-label" name="inline-add-on"
                                            wire:model="query.{{ $key }}" data-class="filter_input"
                                            @class([$this->getClass('filter_input')]) placeholder="Search...">
                                        <div data-class="filter_select_wrapper" @class([$this->getClass('filter_select_wrapper')])>
                                            <label for="hs-inline-leading-select-country"
                                                data-class="filter_select_label"
                                                @class([$this->getClass('filter_select_label')])>Country</label>
                                            <select wire:model="filterBy.{{ $key }}"
                                                id="hs-inline-leading-select-country"
                                                name="hs-inline-leading-select-country" data-class="filter_select"
                                                @class([$this->getClass('filter_select')])>
                                                <option disabled value="">Choose</option>
                                                @foreach ($this->filterByColumn as $key => $item)
                                                    <option value="{{ $key }}">
                                                        {{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if (!$loop->first)
                                        <div data-class="filter_delete_button_wrapper" @class([$this->getClass('filter_delete_button_wrapper')])>
                                            <button type="button" wire:click="deleteFilter({{ $loop->index }})"
                                                data-class="filter_delete_button" @class([$this->getClass('filter_delete_button')])
                                                title="close">
                                                <svg data-class="filter_delete_button_icon"
                                                    @class([$this->getClass('filter_delete_button_icon')]) xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24" viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="m12 13.4l-4.9 4.9q-.275.275-.7.275t-.7-.275t-.275-.7t.275-.7l4.9-4.9l-4.9-4.9q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l4.9 4.9l4.9-4.9q.275-.275.7-.275t.7.275t.275.7t-.275.7L13.4 12l4.9 4.9q.275.275.275.7t-.275.7t-.7.275t-.7-.275z" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div data-class="filter_actions" @class([$this->getClass('filter_actions')])>
                        <button type="button" wire:click="addFilter" wire:loading.attr="disabled"
                            data-class="filter_add_button" @class([$this->getClass('filter_add_button')])
                            {{ $disabledAddFilterButton ? 'disabled' : '' }}>
                            <svg data-class="filter_add_button_icon" @class([$this->getClass('filter_add_button_icon')])
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2z" />
                            </svg>
                            <span wire:loading.remove wire:target="addFilter">Filter..</span>
                            <span wire:loading wire:target="addFilter">Loading..</span>
                        </button>
                        <button type="button" wire:click="resetFilter" data-class="filter_reset_button"
                            @class([$this->getClass('filter_reset_button')])>
                            <svg data-class="filter_reset_button_icon" @class([$this->getClass('filter_reset_button_icon')])
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3s-3 1.331-3 3s1.329 3 3 3" />
                                <path fill="currentColor"
                                    d="M20.817 11.186a8.9 8.9 0 0 0-1.355-3.219a9 9 0 0 0-2.43-2.43a9 9 0 0 0-3.219-1.355a9 9 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a7 7 0 0 1 2.502 1.053a7 7 0 0 1 1.892 1.892A6.97 6.97 0 0 1 19 13a7 7 0 0 1-.55 2.725a7 7 0 0 1-.644 1.188a7 7 0 0 1-.858 1.039a7.03 7.03 0 0 1-3.536 1.907a7.1 7.1 0 0 1-2.822 0a7 7 0 0 1-2.503-1.054a7 7 0 0 1-1.89-1.89A7 7 0 0 1 5 13H3a9 9 0 0 0 1.539 5.034a9.1 9.1 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9 9 0 0 0 1.814-.183a9 9 0 0 0 3.218-1.355a9 9 0 0 0 1.331-1.099a9 9 0 0 0 1.1-1.332A8.95 8.95 0 0 0 21 13a9 9 0 0 0-.183-1.814" />
                            </svg>
                            Reset
                        </button>
                        <button type="button" wire:click="filterData" data-class="filter_apply_button"
                            @class([$this->getClass('filter_apply_button')])>
                            <svg data-class="filter_apply_button_icon" @class([$this->getClass('filter_apply_button_icon')])
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M6.532 4.75h6.936c.457 0 .854 0 1.165.03c.307.028.685.095.993.348c.397.326.621.814.624 1.322c.002.39-.172.726-.34.992c-.168.27-.411.59-.695.964l-.031.04l-.01.013l-2.555 3.369c-.252.332-.315.42-.359.51a1.2 1.2 0 0 0-.099.297c-.02.1-.023.212-.023.634v4.243c0 .208 0 .412-.014.578c-.015.164-.052.427-.224.663c-.21.287-.537.473-.9.495c-.302.019-.547-.103-.69-.183c-.144-.08-.309-.195-.476-.31l-.989-.683l-.048-.033c-.191-.131-.403-.276-.562-.477a1.7 1.7 0 0 1-.303-.585c-.071-.244-.07-.5-.07-.738v-2.97c0-.422-.004-.534-.023-.634a1.2 1.2 0 0 0-.1-.297c-.043-.09-.106-.178-.358-.51L4.825 8.459l-.01-.012l-.03-.04c-.284-.375-.527-.695-.696-.965c-.167-.266-.34-.602-.339-.992a1.72 1.72 0 0 1 .624-1.322c.308-.253.686-.32.993-.349c.311-.029.707-.029 1.165-.029m.397 4l1.647 2.17l.035.047c.201.264.361.475.478.715q.154.317.222.665c.051.261.05.527.05.864v2.968c0 .158.001.247.005.314l.006.062a.2.2 0 0 0 .036.073l.041.034c.05.04.12.088.248.176l.941.65V13.21c0-.337 0-.603.051-.864q.068-.347.222-.665c.117-.24.277-.45.478-.715l.035-.046l1.646-2.17zm7.28-1.5c.195-.26.334-.45.43-.604c.08-.126.104-.188.11-.207a.22.22 0 0 0-.057-.134a1 1 0 0 0-.2-.032c-.232-.022-.556-.023-1.06-.023H6.568c-.504 0-.828 0-1.06.023a1 1 0 0 0-.2.032a.22.22 0 0 0-.057.134c.006.019.03.081.11.207c.096.155.235.344.43.604zm1.541 3.25a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 0 1.5h-3a.75.75 0 0 1-.75-.75m-1.5 2.5a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 0 1.5H15a.75.75 0 0 1-.75-.75m-.5 2.5a.75.75 0 0 1 .75-.75h5a.75.75 0 0 1 0 1.5h-5a.75.75 0 0 1-.75-.75m0 2.5a.75.75 0 0 1 .75-.75H17a.75.75 0 0 1 0 1.5h-2.5a.75.75 0 0 1-.75-.75" />
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        @endif
        <div data-class="main_wrapper" @class([
            $filter
                ? $this->getClass('main_wrapper_with_filter')
                : $this->getClass('main_wrapper'),
        ])>
            <div data-class="search_wrapper" @class([$this->getClass('search_wrapper')])>
                <div data-class="controls_layout_top" @class([$this->getClass('controls_layout_top')])>
                    <div data-class="per_page_wrapper" @class([$this->getClass('per_page_wrapper')])>
                        <select name="perPage" wire:model.live="perPage" data-class="per_page_select"
                            @class([$this->getClass('per_page_select')])>
                            @foreach ($pageOptions as $option)
                                <option value="{{ $option }}">
                                    @if ($option === 'all')
                                        All
                                    @else
                                        {{ $option }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <span data-class="per_page_text" @class([$this->getClass('per_page_text')])>Per Page</span>
                    </div>

                </div>
                <div data-class="controls_layout_bottom" @class([$this->getClass('controls_layout_bottom')])>
                    @if ($model !== null && $showFiterButton)
                        <button type="button" wire:click="showFilter" data-class="filter_button"
                            @class([$this->getClass('filter_button')]) title="filter">
                            <svg data-class="filter_button_icon" @class([$this->getClass('filter_button_icon')])
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M6.532 4.75h6.936c.457 0 .854 0 1.165.03c.307.028.685.095.993.348c.397.326.621.814.624 1.322c.002.39-.172.726-.34.992c-.168.27-.411.59-.695.964l-.031.04l-.01.013l-2.555 3.369c-.252.332-.315.42-.359.51a1.2 1.2 0 0 0-.099.297c-.02.1-.023.212-.023.634v4.243c0 .208 0 .412-.014.578c-.015.164-.052.427-.224.663c-.21.287-.537.473-.9.495c-.302.019-.547-.103-.69-.183c-.144-.08-.309-.195-.476-.31l-.989-.683l-.048-.033c-.191-.131-.403-.276-.562-.477a1.7 1.7 0 0 1-.303-.585c-.071-.244-.07-.5-.07-.738v-2.97c0-.422-.004-.534-.023-.634a1.2 1.2 0 0 0-.1-.297c-.043-.09-.106-.178-.358-.51L4.825 8.459l-.01-.012l-.03-.04c-.284-.375-.527-.695-.696-.965c-.167-.266-.34-.602-.339-.992a1.72 1.72 0 0 1 .624-1.322c.308-.253.686-.32.993-.349c.311-.029.707-.029 1.165-.029m.397 4l1.647 2.17l.035.047c.201.264.361.475.478.715q.154.317.222.665c.051.261.05.527.05.864v2.968c0 .158.001.247.005.314l.006.062a.2.2 0 0 0 .036.073l.041.034c.05.04.12.088.248.176l.941.65V13.21c0-.337 0-.603.051-.864q.068-.347.222-.665c.117-.24.277-.45.478-.715l.035-.046l1.646-2.17zm7.28-1.5c.195-.26.334-.45.43-.604c.08-.126.104-.188.11-.207a.22.22 0 0 0-.057-.134a1 1 0 0 0-.2-.032c-.232-.022-.556-.023-1.06-.023H6.568c-.504 0-.828 0-1.06.023a1 1 0 0 0-.2.032a.22.22 0 0 0-.057.134c.006.019.03.081.11.207c.096.155.235.344.43.604zm1.541 3.25a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 0 1.5h-3a.75.75 0 0 1-.75-.75m-1.5 2.5a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 0 1.5H15a.75.75 0 0 1-.75-.75m-.5 2.5a.75.75 0 0 1 .75-.75h5a.75.75 0 0 1 0 1.5h-5a.75.75 0 0 1-.75-.75m0 2.5a.75.75 0 0 1 .75-.75H17a.75.75 0 0 1 0 1.5h-2.5a.75.75 0 0 1-.75-.75" />
                            </svg>
                        </button>
                    @endif
                    @if ($enableExport && in_array(config('livewire-datatable.export.dropdown.position', 'top'), ['top', 'both']))
                        <div data-class="export_dropdown_wrapper" @class([$this->getClass('export_dropdown_wrapper')]) x-data="{ open: false }">
                            <button @click="open = !open" @keydown.escape.window="open = false"
                                @click.outside="open = false" type="button"
                                class="{{ config('livewire-datatable.export.dropdown.trigger_class') }}">
                                <span>{!! config('livewire-datatable.export.dropdown.trigger_text', 'Export') !!}</span>
                                <svg data-class="export_dropdown_arrow" @class([$this->getClass('export_dropdown_arrow')])
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
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
                    <div data-class="search_input_wrapper" @class([$this->getClass('search_input_wrapper')])>
                        <label>
                            <span data-class="search_icon_wrapper" @class([$this->getClass('search_icon_wrapper')])>
                                <svg data-class="search_icon" @class([$this->getClass('search_icon')])
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="search" name="search" wire:model.live.debounce.300ms="search"
                                placeholder="Search..." data-class="search_input" @class([$this->getClass('search_input')]))
                                {{ $filterDataSearch ? 'disabled' : '' }}>
                        </label>
                    </div>
                </div>
            </div>
            <div data-class="table_wrapper" @class([$this->getClass('table_wrapper')])>
                <table data-class="table" @class([$this->getClass('table')])>
                    <thead data-class="thead" @class([$this->getClass('thead')])>
                        <tr data-class="thead_row" @class([$this->getClass('thead_row')])>
                            @foreach ($columns as $key => $column)
                                <th scope="col" data-class="th" @class([$this->getClass('th')])>
                                    @if (in_array($key, $sortable))
                                        <button wire:click="sortBy('{{ $key }}')"
                                            wire:key="sort-{{ $key }}" data-class="th_sort_button"
                                            @class([$this->getClass('th_sort_button')])>
                                            <span>{{ $column }}</span>
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
                                                        <svg data-class="th_sort_icon_active"
                                                            @class([$this->getClass('th_sort_icon_active')])
                                                            xmlns="http://www.w3.org/2000/svg" width="96"
                                                            height="96" viewBox="0 0 24 24">
                                                            <path fill="currentColor" stroke="currentColor"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="m3 16l4 4l4-4m-4 4V4m4 0h10M11 8h7m-7 4h4" />
                                                        </svg>
                                                    @endif
                                                @else
                                                    <svg data-class="th_sort_icon_inactive"
                                                        @class([$this->getClass('th_sort_icon_inactive')])
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
                    </thead>
                    <tbody wire:loading.class="blur" @class([$this->getClass('tbody')])>
                        @forelse($this->getQuery as $index => $item)
                            <tr @class([$this->getClass('tr')]) wire:key="row-{{ $item->id ?? $index }}"
                                id="row-{{ $item->id ?? $index }}">
                                @foreach ($columns as $key => $column)
                                    <td @class([$this->getClass('td')]) wire:key="cell-{{ $key }}">
                                        <div @class([$this->getClass("td_{$key}")])>
                                            @if (isset($customColumns[$key]))
                                                @include($customColumns[$key], [
                                                    'item' => $item,
                                                    'value' => data_get($item, $key),
                                                ])
                                            @else
                                                @if ($key === 'no')
                                                    @if ($sortField === 'no')
                                                        <!-- When sorting by "no" column, show reversed numbering based on sort direction -->
                                                        @if ($sortDirection === 'desc')
                                                            <!-- Show from highest to lowest (descending) -->
                                                            @if (config('livewire-datatable.default_pagination') == 'simplePaginate')
                                                                {{ $this->totals - (($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() + $loop->parent->iteration) + 1 }}
                                                            @else
                                                                {{ $this->getQuery->total() - (($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() + $loop->parent->iteration) + 1 }}
                                                            @endif
                                                        @else
                                                            <!-- Show from lowest to highest (ascending) -->
                                                            {{ $loop->parent->iteration + ($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() }}
                                                        @endif
                                                    @else
                                                        <!-- When not sorting by "no", always show sequential numbering -->
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
                    <div class="dark">
                        {{ $this->getQuery->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
