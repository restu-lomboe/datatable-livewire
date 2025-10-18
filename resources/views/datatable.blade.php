<div>
    <div @class([$this->getClass('wrapper')])>
        <div @class([$this->getClass('search_wrapper')])>
            <div @class([$this->getClass('controls_wrapper')])>
                <div class="flex items-center gap-4">
                    <div @class([$this->getClass('per_page_wrapper')])>
                        <select name="perPage" wire:model.live="perPage" @class([$this->getClass('per_page_select')])>
                            @foreach ($pageOptions as $option)
                                <option value="{{ $option }}">
                                    @if ($option === 'all')
                                        Show all records
                                    @else
                                        {{ $option }} per page
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($enableExport && in_array(config('livewire-datatable.export.dropdown.position', 'top'), ['top', 'both']))
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @keydown.escape.window="open = false"
                                @click.outside="open = false" type="button"
                                class="{{ config('livewire-datatable.export.dropdown.trigger_class') }}">
                                <span>{!! config('livewire-datatable.export.dropdown.trigger_text', 'Export') !!}</span>
                                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
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
                </div>
                <div @class([$this->getClass('search_input_wrapper')])>
                    <label>
                        <span @class([$this->getClass('search_icon_wrapper')])>
                            <svg @class([$this->getClass('search_icon')]) xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="search" name="search" wire:model.live.debounce.300ms="search"
                            placeholder="Search..." @class([$this->getClass('search_input')])>
                    </label>
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
                                                        xmlns="http://www.w3.org/2000/svg" width="96" height="96"
                                                        viewBox="0 0 24 24">
                                                        <path fill="none" stroke="currentColor"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="m3 8l4-4l4 4M7 4v16m4-8h4m-4 4h7m-7 4h10" />
                                                    </svg>
                                                @else
                                                    <svg class="size-4 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                                        width="96" height="96" viewBox="0 0 24 24">
                                                        <path fill="currentColor" stroke="currentColor"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="m3 16l4 4l4-4m-4 4V4m4 0h10M11 8h7m-7 4h4" />
                                                    </svg>
                                                @endif
                                            @else
                                                <svg class="size-4 text-gray-400 dark:text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-200"
                                                    xmlns="http://www.w3.org/2000/svg" width="96" height="96"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor" stroke="currentColor"
                                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="m3 16l4 4l4-4m-4 4V4m4 0h10M11 8h7m-7 4h4" />
                                                </svg>
                                            @endif
                                        </span>
                                    </button>
                                @else
                                    <span class="text-gray-700 dark:text-gray-200 capitalize">{{ $column }}</span>
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
                                <td @class([
                                    $this->getClass('td'),
                                    $this->getClass("td_{$key}"), // Column-specific class
                                ]) wire:key="cell-{{ $key }}">
                                    @if (isset($customColumns[$key]))
                                        @include($customColumns[$key], [
                                            'item' => $item,
                                            'value' => data_get($item, $key),
                                        ])
                                    @else
                                        @if ($key === 'no')
                                            <!-- check sortDirection -->
                                            @if ($sortDirection === 'desc')
                                                {{ $loop->parent->iteration + ($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() }}
                                            @elseif ($sortDirection === 'asc')
                                                <!-- check config('livewire-datatable.default_pagination') == 'simplePaginate' -->
                                                @if (config('livewire-datatable.default_pagination') == 'simplePaginate')
                                                    {{ $this->totals - (($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() + $loop->parent->iteration) + 1 }}
                                                @else
                                                    {{ $this->getQuery->total() - (($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() + $loop->parent->iteration) + 1 }}
                                                @endif
                                            @endif
                                        @else
                                            {!! $this->formatValue($key, data_get($item, $key)) !!}
                                        @endif
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" @class([$this->getClass('empty_wrapper')])>
                                <div @class([$this->getClass('empty_content')])>
                                    <svg @class([$this->getClass('empty_icon')]) xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
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

        <div class="mt-4">
            <div class="dark">
                {{ $this->getQuery->links() }}
            </div>
        </div>
    </div>
</div>
