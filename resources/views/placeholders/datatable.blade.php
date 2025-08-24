<div>
    <div @class([
        config('livewire-datatable.theme')['wrapper'],
        'animate-pulse',
    ])>
        <div @class([config('livewire-datatable.theme')['search_wrapper']])>
            <div @class([config('livewire-datatable.theme')['controls_wrapper']])>
                <div @class([config('livewire-datatable.theme')['per_page_wrapper']])>
                    <select name="perPage" wire:model.live="perPage" @class([config('livewire-datatable.theme')['per_page_select']])>
                        <option value="">---</option>
                    </select>
                </div>
                <div @class([config('livewire-datatable.theme')['search_input_wrapper']])>
                    <label class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search..."
                            @class([config('livewire-datatable.theme')['search_input']])>
                    </label>
                </div>
            </div>
        </div>

        <div @class([config('livewire-datatable.theme')['table_wrapper']])>
            <table @class([config('livewire-datatable.theme')['table']])>
                <thead @class([config('livewire-datatable.theme')['thead']])>
                    <tr @class([config('livewire-datatable.theme')['thead_row']])>
                        <th @class([config('livewire-datatable.theme')['th']])>#</th>
                        @foreach ($columns as $key => $column)
                            <th scope="col" @class([config('livewire-datatable.theme')['th']])>
                                @if (in_array($key, $sortable))
                                    <button wire:click="sortBy['{{ $key }}']"
                                        wire:key="sort-{{ $key }}" @class([config('livewire-datatable.theme')['th_sort_button']])>
                                        <span>{{ $column }}</span>
                                        <span @class([config('livewire-datatable.theme')['th_sort_icon_wrapper']])>
                                            @if ($sortField === $key)
                                                @if ($sortDirection === 'asc')
                                                    <svg @class([config('livewire-datatable.theme')['th_sort_icon_active']])
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
                <tbody @class([config('livewire-datatable.theme')['tbody']])>
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" @class([config('livewire-datatable.theme')['empty_wrapper']])>
                            <div @class([config('livewire-datatable.theme')['empty_content']])>
                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="2" r="0" fill="currentColor">
                                        <animate attributeName="r" begin="0" calcMode="spline" dur="1s"
                                            keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8"
                                            repeatCount="indefinite" values="0;2;0;0" />
                                    </circle>
                                    <circle cx="12" cy="2" r="0" fill="currentColor"
                                        transform="rotate(45 12 12)">
                                        <animate attributeName="r" begin="0.125s" calcMode="spline" dur="1s"
                                            keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8"
                                            repeatCount="indefinite" values="0;2;0;0" />
                                    </circle>
                                    <circle cx="12" cy="2" r="0" fill="currentColor"
                                        transform="rotate(90 12 12)">
                                        <animate attributeName="r" begin="0.25s" calcMode="spline" dur="1s"
                                            keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8"
                                            repeatCount="indefinite" values="0;2;0;0" />
                                    </circle>
                                    <circle cx="12" cy="2" r="0" fill="currentColor"
                                        transform="rotate(135 12 12)">
                                        <animate attributeName="r" begin="0.375s" calcMode="spline" dur="1s"
                                            keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8"
                                            repeatCount="indefinite" values="0;2;0;0" />
                                    </circle>
                                    <circle cx="12" cy="2" r="0" fill="currentColor"
                                        transform="rotate(180 12 12)">
                                        <animate attributeName="r" begin="0.5s" calcMode="spline" dur="1s"
                                            keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8"
                                            repeatCount="indefinite" values="0;2;0;0" />
                                    </circle>
                                    <circle cx="12" cy="2" r="0" fill="currentColor"
                                        transform="rotate(225 12 12)">
                                        <animate attributeName="r" begin="0.625s" calcMode="spline" dur="1s"
                                            keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8"
                                            repeatCount="indefinite" values="0;2;0;0" />
                                    </circle>
                                    <circle cx="12" cy="2" r="0" fill="currentColor"
                                        transform="rotate(270 12 12)">
                                        <animate attributeName="r" begin="0.75s" calcMode="spline" dur="1s"
                                            keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8"
                                            repeatCount="indefinite" values="0;2;0;0" />
                                    </circle>
                                    <circle cx="12" cy="2" r="0" fill="currentColor"
                                        transform="rotate(315 12 12)">
                                        <animate attributeName="r" begin="0.875s" calcMode="spline" dur="1s"
                                            keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8"
                                            repeatCount="indefinite" values="0;2;0;0" />
                                    </circle>
                                </svg>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
