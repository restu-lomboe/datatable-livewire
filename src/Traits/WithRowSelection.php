<?php

namespace Developerawam\LivewireDatatable\Traits;

use Livewire\Attributes\Locked;

trait WithRowSelection
{
    #[Locked]
    public $rowKey;

    #[Locked]
    public $selected = [];

    #[Locked]
    public $selectAllMatching = false;

    #[Locked]
    public $excludedKeys = [];

    /**
     * Resolve the column used as the stable row identifier.
     */
    protected function resolveRowKey(?string $rowKey = null): void
    {
        if ($rowKey !== null && $rowKey !== '') {
            $this->rowKey = $rowKey;

            return;
        }

        $configured = config('livewire-datatable.selection.row_key');

        if (is_string($configured) && $configured !== '') {
            $this->rowKey = $configured;

            return;
        }

        $this->rowKey = $this->model ? (new $this->model)->getKeyName() : 'id';
    }

    /**
     * Return the first column using the checkbox formatter.
     */
    public function getCheckboxColumnKey(): ?string
    {
        foreach (array_keys($this->columns) as $key) {
            if ($this->isCheckboxFormatter($key)) {
                return (string) $key;
            }
        }

        return null;
    }

    public function isCheckboxColumn(string $key): bool
    {
        return $this->getCheckboxColumnKey() === $key;
    }

    protected function renderSelectionCheckbox($item, $key = null): string
    {
        $key = $key ?? $this->getCheckboxColumnKey();

        if ($key === null || ! $this->isCheckboxFormatter($key)) {
            return '';
        }

        $rowKey = $this->getRowKeyValue($item);

        if ($rowKey === null) {
            return '';
        }

        return view('livewire-datatable::partials.checkbox', [
            'rowKey' => $rowKey,
            'checked' => $this->isRowSelected($rowKey),
            'label' => (string) ($this->columns[$key] ?? 'Select row'),
            'class' => $this->getClass('selection_checkbox'),
        ])->render();
    }

    /**
     * Resolve a row identifier as a string so it can be stored safely in Livewire state.
     */
    public function getRowKeyValue($item): ?string
    {
        if ($item === null) {
            return null;
        }

        $value = data_get($item, $this->rowKey ?: 'id');

        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    /**
     * Keys visible on the current page. Used by the header checkbox.
     */
    public function getPageKeys(): array
    {
        return collect($this->getQuery->items())
            ->map(fn ($item) => $this->getRowKeyValue($item))
            ->filter(fn ($key) => $key !== null)
            ->unique()
            ->values()
            ->all();
    }

    public function isRowSelected($key): bool
    {
        $key = $this->normalizeSelectionKey($key);

        if ($key === null) {
            return false;
        }

        if ($this->selectAllMatching) {
            return ! in_array($key, $this->excludedKeys, true);
        }

        return in_array($key, $this->selected, true);
    }

    public function isPageSelected(): bool
    {
        $keys = $this->getPageKeys();

        if ($keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            if (! $this->isRowSelected($key)) {
                return false;
            }
        }

        return true;
    }

    public function isPageIndeterminate(): bool
    {
        $keys = $this->getPageKeys();

        if ($keys === []) {
            return false;
        }

        $selected = 0;

        foreach ($keys as $key) {
            if ($this->isRowSelected($key)) {
                $selected++;
            }
        }

        return $selected > 0 && $selected < count($keys);
    }

    public function hasSelection(): bool
    {
        return $this->selectAllMatching || ! empty($this->selected);
    }

    public function getSelectionCount(): int
    {
        if ($this->selectAllMatching) {
            return max(0, $this->getTotalCount() - count($this->excludedKeys));
        }

        return count($this->selected);
    }

    public function getTotalCount(): int
    {
        try {
            $query = $this->getQuery;
        } catch (\Throwable $e) {
            return 0;
        }

        if (method_exists($query, 'total')) {
            return (int) $query->total();
        }

        return (int) ($this->totals ?? count($query->items()));
    }

    public function canSelectAllMatching(): bool
    {
        return $this->model !== null
            && ! $this->selectAllMatching
            && $this->hasSelection()
            && $this->getTotalCount() > $this->getSelectionCount();
    }

    /**
     * Toggle one row. Keys outside the current page are ignored to prevent
     * client-supplied identifiers from entering selection state.
     */
    public function toggleRow($key): void
    {
        $key = $this->normalizeSelectionKey($key);

        if ($key === null || ! in_array($key, $this->getPageKeys(), true)) {
            return;
        }

        if ($this->selectAllMatching) {
            $index = array_search($key, $this->excludedKeys, true);

            if ($index !== false) {
                unset($this->excludedKeys[$index]);
                $this->excludedKeys = array_values($this->excludedKeys);
            } else {
                $this->excludedKeys[] = $key;
            }
        } else {
            $index = array_search($key, $this->selected, true);

            if ($index !== false) {
                unset($this->selected[$index]);
                $this->selected = array_values($this->selected);
            } elseif (count($this->selected) < $this->maxSelectionKeys()) {
                $this->selected[] = $key;
            }
        }

        $this->dispatchSelectionChanged();
    }

    /**
     * Toggle every row on the current page (not every matching row).
     */
    public function toggleSelectPage(): void
    {
        $pageKeys = $this->getPageKeys();

        if ($pageKeys === []) {
            return;
        }

        $allSelected = true;

        foreach ($pageKeys as $key) {
            if (! $this->isRowSelected($key)) {
                $allSelected = false;
                break;
            }
        }

        if ($this->selectAllMatching) {
            if ($allSelected) {
                $this->excludedKeys = array_values(array_unique(array_merge($this->excludedKeys, $pageKeys)));
            } else {
                $this->excludedKeys = array_values(array_diff($this->excludedKeys, $pageKeys));
            }
        } elseif ($allSelected) {
            $this->selected = array_values(array_diff($this->selected, $pageKeys));
        } else {
            $selected = array_values(array_unique(array_merge($this->selected, $pageKeys)));
            $this->selected = array_slice($selected, 0, $this->maxSelectionKeys());
        }

        $this->dispatchSelectionChanged();
    }

    /**
     * Select every row matching the current search/filter without enumerating keys.
     */
    public function selectAllMatching(): void
    {
        if ($this->model === null || $this->getTotalCount() <= 0) {
            return;
        }

        $this->selectAllMatching = true;
        $this->selected = [];
        $this->excludedKeys = [];

        $this->dispatchSelectionChanged();
    }

    public function clearSelection(): void
    {
        $this->resetSelectionState();

        $this->dispatchSelectionChanged(true);
    }

    /**
     * Public action for components configured with dispatch=manual.
     */
    public function notifySelection(): void
    {
        $this->dispatchSelectionChanged(true);
    }

    protected function clearSelectionOnQueryChange(): void
    {
        if (! config('livewire-datatable.selection.clear_on_query_change', true)) {
            return;
        }

        if ($this->hasSelection()) {
            $this->resetSelectionState();
            // Always notify on a query change so parents do not keep stale selection.
            $this->dispatchSelectionChanged(true);
        }
    }

    protected function resetSelectionState(): void
    {
        $this->selected = [];
        $this->excludedKeys = [];
        $this->selectAllMatching = false;
    }

    protected function dispatchSelectionChanged(bool $force = false): void
    {
        $dispatchMode = config('livewire-datatable.selection.dispatch', 'change');

        if (! $force && $dispatchMode !== 'change') {
            return;
        }

        $event = config('livewire-datatable.selection.event', 'datatable-selection-changed');

        if (! is_string($event) || $event === '') {
            return;
        }

        $this->dispatch($event, selection: $this->getSelectionPayload());
    }

    /**
     * @return array<string, mixed>
     */
    public function getSelectionPayload(): array
    {
        $maxKeys = $this->maxSelectionKeys();
        $keys = array_values($this->selected);
        $except = array_values($this->excludedKeys);
        $truncated = false;

        if (! $this->selectAllMatching && count($keys) > $maxKeys) {
            $keys = array_slice($keys, 0, $maxKeys);
            $truncated = true;
        }

        if (count($except) > $maxKeys) {
            $except = array_slice($except, 0, $maxKeys);
            $truncated = true;
        }

        $payload = [
            'table' => $this->getId(),
            'rowKey' => $this->rowKey ?: 'id',
            'keys' => $keys,
            'allMatching' => (bool) $this->selectAllMatching,
            'except' => $except,
            'count' => $this->getSelectionCount(),
            'truncated' => $truncated,
            'context' => $this->getSelectionContext(),
        ];

        $payloadMode = config('livewire-datatable.selection.payload', 'keys');

        if (in_array($payloadMode, ['rows', 'both'], true)) {
            $rows = $this->getSelectedRowsPayload();
            $payload['rows'] = $rows['rows'];
            $payload['truncated'] = $payload['truncated'] || $rows['truncated'];
        }

        return $payload;
    }

    /**
     * @return array{rows: array<int, array<string, mixed>>, truncated: bool}
     */
    protected function getSelectedRowsPayload(): array
    {
        $maxRows = max(1, (int) config('livewire-datatable.selection.max_rows', 100));

        if ($this->selectAllMatching) {
            // Enumerating every matching row would defeat the purpose of allMatching.
            return ['rows' => [], 'truncated' => true];
        }

        if (empty($this->selected)) {
            return ['rows' => [], 'truncated' => false];
        }

        $keys = array_values($this->selected);
        $rowKey = $this->rowKey ?: 'id';

        if ($this->model !== null && ! str_contains($rowKey, '.')) {
            $query = $this->buildScopedQuery();
            $query->whereIn($rowKey, $keys);

            $items = $query->limit($maxRows + 1)->get();

            return [
                'rows' => $items->take($maxRows)->map(fn ($item) => $this->mapSelectionRow($item))->values()->all(),
                'truncated' => $items->count() > $maxRows,
            ];
        }

        $pageItems = collect($this->getQuery->items())
            ->keyBy(fn ($item) => $this->getRowKeyValue($item));

        $rows = [];

        foreach (array_slice($keys, 0, $maxRows) as $key) {
            $item = $pageItems->get($key);

            if ($item !== null) {
                $rows[] = $this->mapSelectionRow($item);
            }
        }

        return [
            'rows' => $rows,
            'truncated' => count($keys) > $maxRows || count($rows) < min(count($keys), $maxRows),
        ];
    }

    /**
     * Only expose columns that are displayed in the table unless the app opts in
     * to a custom allowlist through selection.columns.
     *
     * @return array{key: ?string, values: array<string, mixed>}
     */
    protected function mapSelectionRow($item): array
    {
        $key = $this->getRowKeyValue($item);
        $columns = config('livewire-datatable.selection.columns', []);

        if (! is_array($columns) || $columns === []) {
            $columns = array_values(array_filter(array_keys($this->columns), function ($column) {
                return ! $this->isCheckboxFormatter($column)
                    && $column !== 'no'
                    && ! in_array($column, ['action', 'actions'], true);
            }));
        }

        $values = [];

        foreach ($columns as $column) {
            $values[$column] = data_get($item, $column);
        }

        if ($this->rowKey && ! array_key_exists($this->rowKey, $values)) {
            $values[$this->rowKey] = $key;
        }

        return [
            'key' => $key,
            'values' => $values,
        ];
    }

    /**
     * Lightweight context so parents can re-run an all-matching query later.
     *
     * @return array<string, mixed>
     */
    protected function getSelectionContext(): array
    {
        return [
            'search' => $this->search,
            'filter' => $this->filterDataSearch
                ? ['columns' => $this->filterBy, 'values' => $this->query]
                : null,
            'date' => $this->dateFilterEnabled
                ? [
                    'column' => $this->dateFilterColumn,
                    'start' => $this->dateFilterStart,
                    'end' => $this->dateFilterEnd,
                ]
                : null,
            'sort' => [
                'field' => $this->sortField,
                'direction' => $this->sortDirection,
            ],
        ];
    }

    protected function normalizeSelectionKey($key): ?string
    {
        if (is_array($key) || is_object($key)) {
            return null;
        }

        $key = (string) $key;

        return $key === '' ? null : $key;
    }

    protected function maxSelectionKeys(): int
    {
        return max(1, (int) config('livewire-datatable.selection.max_keys', 5000));
    }
}
