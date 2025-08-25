<div>
    <div @class([$this->getClass('wrapper')])>
        <div @class([$this->getClass('search_wrapper')])>
            <div @class([$this->getClass('controls_wrapper')])>
                <div @class([$this->getClass('per_page_wrapper')])>
                    <select name="perPage" wire:model.live="perPage" @class([$this->getClass('per_page_select')])>
                        @foreach ($pageOptions as $option)
                            <option value="{{ $option }}">{{ $option }} per page </option>
                        @endforeach
                    </select>
                </div>
                <div @class([$this->getClass('search_input_wrapper')])>
                    <label class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search..."
                            @class([$this->getClass('search_input')])>
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
                        <tr @class([$this->getClass('tr')]) wire:key="row-{{ $item->getKey() }}"
                            id="row-{{ $item->getKey() }}">
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
                                            {{ data_get($item, $key) }}
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
