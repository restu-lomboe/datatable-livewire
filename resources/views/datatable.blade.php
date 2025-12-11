<div>
    <div @class([$this->getClass('wrapper'), ' grid grid-cols-4'])>
        @if ($filter)
            <div class="transition duration-300 ease-in-out p-4 border-r border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">View</p>
                    <button type="button" wire:click="closeFilter"
                        class="inline-flex items-center text-sm font-medium text-gray-800 disabled:pointer-events-none dark:text-white cursor-pointer hover:text-gray-500 dark:hover:text-gray-300"
                        title="close">
                        <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="m12 13.4l-4.9 4.9q-.275.275-.7.275t-.7-.275t-.275-.7t.275-.7l4.9-4.9l-4.9-4.9q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l4.9 4.9l4.9-4.9q.275-.275.7-.275t.7.275t.275.7t-.275.7L13.4 12l4.9 4.9q.275.275.275.7t-.275.7t-.7.275t-.7-.275z" />
                        </svg>
                    </button>
                </div>

                <div class="flex flex-col mt-4 space-y-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Filter list by: </span>
                    <div class="list-filter">
                        <div class="max-w-sm space-y-3">
                            @foreach ($filterBy as $key => $item)
                                <div class="flex gap-2 items-center justify-between">
                                    <div class="relative">
                                        <input type="text" id="hs-inline-leading-select-label" name="inline-add-on"
                                            wire:model="query.{{ $key }}"
                                            class="py-2.5 sm:py-3 px-4 ps-33 block w-full border-gray-200 rounded-lg sm:text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:placeholder-gray-500 dark:focus:ring-gray-600"
                                            placeholder="search">
                                        <div class="absolute inset-y-0 start-0 flex items-center text-gray-500">
                                            <label for="hs-inline-leading-select-country"
                                                class="sr-only">Country</label>
                                            <select wire:model="filterBy.{{ $key }}"
                                                id="hs-inline-leading-select-country"
                                                name="hs-inline-leading-select-country"
                                                class="text-sm block w-30 border-l border-gray-200 dark:border-gray-700 rounded dark:text-gray-500 dark:bg-gray-900 py-3">
                                                <option disabled value="">Choose</option>
                                                @foreach ($this->filterByColumn as $key => $item)
                                                    <option value="{{ $key }}">
                                                        {{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if (!$loop->first)
                                        <div class="">
                                            <button type="button" wire:click="deleteFilter({{ $loop->index }})"
                                                class="inline-flex items-center text-xs font-medium text-gray-800 disabled:pointer-events-none dark:text-white cursor-pointer hover:text-red-500 dark:hover:text-red-300"
                                                title="close">
                                                <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg"
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
                    <div class="flex items-center justify-between">
                        <button type="button" wire:click="addFilter" wire:loading.attr="disabled"
                            class="py-2 pl-2 pr-3 inline-flex items-center gap-x-1 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 dark:focus:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
                            {{ $disabledAddFilterButton ? 'disabled' : '' }}>
                            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2z" />
                            </svg>
                            <span wire:loading.remove wire:target="addFilter">Filter..</span>
                            <span wire:loading wire:target="addFilter">Loading..</span>
                        </button>
                        <button type="button" wire:click="resetFilter"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-yellow-200 bg-white text-yellow-800 shadow-2xs hover:bg-yellow-50 focus:outline-hidden focus:bg-yellow-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-800 dark:border-gray-700 dark:text-yellow-400 dark:hover:bg-gray-700 dark:focus:bg-gray-700">
                            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M12 16c1.671 0 3-1.331 3-3s-1.329-3-3-3s-3 1.331-3 3s1.329 3 3 3" />
                                <path fill="currentColor"
                                    d="M20.817 11.186a8.9 8.9 0 0 0-1.355-3.219a9 9 0 0 0-2.43-2.43a9 9 0 0 0-3.219-1.355a9 9 0 0 0-1.838-.18V2L8 5l3.975 3V6.002c.484-.002.968.044 1.435.14a7 7 0 0 1 2.502 1.053a7 7 0 0 1 1.892 1.892A6.97 6.97 0 0 1 19 13a7 7 0 0 1-.55 2.725a7 7 0 0 1-.644 1.188a7 7 0 0 1-.858 1.039a7.03 7.03 0 0 1-3.536 1.907a7.1 7.1 0 0 1-2.822 0a7 7 0 0 1-2.503-1.054a7 7 0 0 1-1.89-1.89A7 7 0 0 1 5 13H3a9 9 0 0 0 1.539 5.034a9.1 9.1 0 0 0 2.428 2.428A8.95 8.95 0 0 0 12 22a9 9 0 0 0 1.814-.183a9 9 0 0 0 3.218-1.355a9 9 0 0 0 1.331-1.099a9 9 0 0 0 1.1-1.332A8.95 8.95 0 0 0 21 13a9 9 0 0 0-.183-1.814" />
                            </svg>
                            Reset
                        </button>
                        <button type="button" wire:click="filterData"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-green-200 bg-white text-green-800 shadow-2xs hover:bg-green-50 focus:outline-hidden focus:bg-green-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-800 dark:border-gray-700 dark:text-green-400 dark:hover:bg-gray-700 dark:focus:bg-gray-700">
                            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M6.532 4.75h6.936c.457 0 .854 0 1.165.03c.307.028.685.095.993.348c.397.326.621.814.624 1.322c.002.39-.172.726-.34.992c-.168.27-.411.59-.695.964l-.031.04l-.01.013l-2.555 3.369c-.252.332-.315.42-.359.51a1.2 1.2 0 0 0-.099.297c-.02.1-.023.212-.023.634v4.243c0 .208 0 .412-.014.578c-.015.164-.052.427-.224.663c-.21.287-.537.473-.9.495c-.302.019-.547-.103-.69-.183c-.144-.08-.309-.195-.476-.31l-.989-.683l-.048-.033c-.191-.131-.403-.276-.562-.477a1.7 1.7 0 0 1-.303-.585c-.071-.244-.07-.5-.07-.738v-2.97c0-.422-.004-.534-.023-.634a1.2 1.2 0 0 0-.1-.297c-.043-.09-.106-.178-.358-.51L4.825 8.459l-.01-.012l-.03-.04c-.284-.375-.527-.695-.696-.965c-.167-.266-.34-.602-.339-.992a1.72 1.72 0 0 1 .624-1.322c.308-.253.686-.32.993-.349c.311-.029.707-.029 1.165-.029m.397 4l1.647 2.17l.035.047c.201.264.361.475.478.715q.154.317.222.665c.051.261.05.527.05.864v2.968c0 .158.001.247.005.314l.006.062a.2.2 0 0 0 .036.073l.041.034c.05.04.12.088.248.176l.941.65V13.21c0-.337 0-.603.051-.864q.068-.347.222-.665c.117-.24.277-.45.478-.715l.035-.046l1.646-2.17zm7.28-1.5c.195-.26.334-.45.43-.604c.08-.126.104-.188.11-.207a.22.22 0 0 0-.057-.134a1 1 0 0 0-.2-.032c-.232-.022-.556-.023-1.06-.023H6.568c-.504 0-.828 0-1.06.023a1 1 0 0 0-.2.032a.22.22 0 0 0-.057.134c.006.019.03.081.11.207c.096.155.235.344.43.604zm1.541 3.25a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 0 1.5h-3a.75.75 0 0 1-.75-.75m-1.5 2.5a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 0 1.5H15a.75.75 0 0 1-.75-.75m-.5 2.5a.75.75 0 0 1 .75-.75h5a.75.75 0 0 1 0 1.5h-5a.75.75 0 0 1-.75-.75m0 2.5a.75.75 0 0 1 .75-.75H17a.75.75 0 0 1 0 1.5h-2.5a.75.75 0 0 1-.75-.75" />
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        @endif
        <div class="{{ $filter ? 'col-span-3' : 'col-span-4' }}">
            <div @class([$this->getClass('search_wrapper')])>
                <div @class([$this->getClass('controls_wrapper')])>
                    <div class="flex items-center justify-between gap-4">
                        <div @class([$this->getClass('per_page_wrapper')])>
                            <select name="perPage" wire:model.live="perPage" @class([$this->getClass('per_page_select')])>
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
                            <span class="text-sm text-gray-400 dark:text-gray-500">Per Page</span>
                        </div>

                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <button type="button" wire:click="showFilter"
                            class="py-2 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-sm border border-gray-200 text-gray-800 hover:border-gray-500 hover:text-gray-500 focus:outline-hidden focus:border-gray-500 focus:text-gray-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-700 dark:text-white dark:hover:text-gray-300 dark:hover:border-gray-300"
                            title="filter">
                            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M6.532 4.75h6.936c.457 0 .854 0 1.165.03c.307.028.685.095.993.348c.397.326.621.814.624 1.322c.002.39-.172.726-.34.992c-.168.27-.411.59-.695.964l-.031.04l-.01.013l-2.555 3.369c-.252.332-.315.42-.359.51a1.2 1.2 0 0 0-.099.297c-.02.1-.023.212-.023.634v4.243c0 .208 0 .412-.014.578c-.015.164-.052.427-.224.663c-.21.287-.537.473-.9.495c-.302.019-.547-.103-.69-.183c-.144-.08-.309-.195-.476-.31l-.989-.683l-.048-.033c-.191-.131-.403-.276-.562-.477a1.7 1.7 0 0 1-.303-.585c-.071-.244-.07-.5-.07-.738v-2.97c0-.422-.004-.534-.023-.634a1.2 1.2 0 0 0-.1-.297c-.043-.09-.106-.178-.358-.51L4.825 8.459l-.01-.012l-.03-.04c-.284-.375-.527-.695-.696-.965c-.167-.266-.34-.602-.339-.992a1.72 1.72 0 0 1 .624-1.322c.308-.253.686-.32.993-.349c.311-.029.707-.029 1.165-.029m.397 4l1.647 2.17l.035.047c.201.264.361.475.478.715q.154.317.222.665c.051.261.05.527.05.864v2.968c0 .158.001.247.005.314l.006.062a.2.2 0 0 0 .036.073l.041.034c.05.04.12.088.248.176l.941.65V13.21c0-.337 0-.603.051-.864q.068-.347.222-.665c.117-.24.277-.45.478-.715l.035-.046l1.646-2.17zm7.28-1.5c.195-.26.334-.45.43-.604c.08-.126.104-.188.11-.207a.22.22 0 0 0-.057-.134a1 1 0 0 0-.2-.032c-.232-.022-.556-.023-1.06-.023H6.568c-.504 0-.828 0-1.06.023a1 1 0 0 0-.2.032a.22.22 0 0 0-.057.134c.006.019.03.081.11.207c.096.155.235.344.43.604zm1.541 3.25a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 0 1.5h-3a.75.75 0 0 1-.75-.75m-1.5 2.5a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 0 1.5H15a.75.75 0 0 1-.75-.75m-.5 2.5a.75.75 0 0 1 .75-.75h5a.75.75 0 0 1 0 1.5h-5a.75.75 0 0 1-.75-.75m0 2.5a.75.75 0 0 1 .75-.75H17a.75.75 0 0 1 0 1.5h-2.5a.75.75 0 0 1-.75-.75" />
                            </svg>
                        </button>
                        @if ($enableExport && in_array(config('livewire-datatable.export.dropdown.position', 'top'), ['top', 'both']))
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @keydown.escape.window="open = false"
                                    @click.outside="open = false" type="button"
                                    class="{{ config('livewire-datatable.export.dropdown.trigger_class') }}">
                                    <span>{!! config('livewire-datatable.export.dropdown.trigger_text', 'Export') !!}</span>
                                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
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
                        <div @class([$this->getClass('search_input_wrapper')])>
                            <label>
                                <span @class([$this->getClass('search_icon_wrapper')])>
                                    <svg @class([$this->getClass('search_icon')]) xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="search" name="search" wire:model.live.debounce.300ms="search"
                                    placeholder="Search..." @class([$this->getClass('search_input')])
                                    {{ $filterDataSearch ? 'disabled' : '' }}>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div @class([$this->getClass('table_wrapper')])>
                <table @class([$this->getClass('table')])>
                    <thead @class([$this->getClass('thead')])>
                        <tr @class([$this->getClass('thead_row')])>
                            @foreach ($columns as $key => $column)
                                <th scope="col" @class([$this->getClass('th')])>
                                    @if (in_array($key, $sortable))
                                        <button wire:click="sortBy('{{ $key }}')"
                                            wire:key="sort-{{ $key }}" @class([$this->getClass('th_sort_button')])>
                                            <span>{{ $column }}</span>
                                            <span @class([$this->getClass('th_sort_icon_wrapper')])>
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
                                                        <svg class="size-4 text-blue-500"
                                                            xmlns="http://www.w3.org/2000/svg" width="96"
                                                            height="96" viewBox="0 0 24 24">
                                                            <path fill="currentColor" stroke="currentColor"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="m3 16l4 4l4-4m-4 4V4m4 0h10M11 8h7m-7 4h4" />
                                                        </svg>
                                                    @endif
                                                @else
                                                    <svg class="size-4 text-gray-400 dark:text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-200"
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
                                        <span
                                            class="text-gray-700 dark:text-gray-200 capitalize">{{ $column }}</span>
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
                                <td colspan="{{ count($columns) }}" @class([$this->getClass('empty_wrapper')])>
                                    <div @class([$this->getClass('empty_content')])>
                                        <svg @class([$this->getClass('empty_icon')]) xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <span @class([$this->getClass('empty_text')])>No records found.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->getQuery->hasPages())
                <div @class([$this->getClass('pagination_wrapper')])>
                    <div class="dark">
                        {{ $this->getQuery->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
