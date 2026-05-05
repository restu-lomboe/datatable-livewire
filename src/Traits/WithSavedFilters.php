<?php

namespace Developerawam\LivewireDatatable\Traits;

/**
 * Trait WithSavedFilters
 *
 * Improvement #8: Simpan kombinasi filter sebagai "preset" yang bisa dipakai ulang.
 * Storage: database (tabel datatable_filter_presets) atau session.
 *
 * Aktifkan dengan: :saved-filters="true"
 */
trait WithSavedFilters
{
    /** Currently loaded presets */
    public array $filterPresets = [];

    /** Name for the preset being saved */
    public string $newPresetName = '';

    /** Whether to show save preset UI */
    public bool $showSavePreset = false;

    /** Whether saved filters feature is enabled */
    public bool $savedFilters = false;

    /**
     * FIX: boot() dihapus — tidak boleh overwrite $savedFilters dari config.
     * $savedFilters adalah Livewire public property yang dipertahankan antar request.
     * loadPresets() dipanggil dari mount() saja (sekali saat inisialisasi).
     */

    /**
     * Save current filter state as a named preset.
     */
    public function saveFilterPreset(): void
    {
        $name = trim($this->newPresetName);

        if (!$name) {
            return;
        }

        $preset = [
            'name' => $name,
            'filterBy' => $this->filterBy,
            'query' => $this->query,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'search' => $this->search,
            'created_at' => now()->toISOString(),
        ];

        $driver = config('livewire-datatable.saved_filters.driver', 'session');

        if ($driver === 'database') {
            $this->savePresetToDatabase($preset);
        } else {
            $this->savePresetToSession($name, $preset);
        }

        $this->newPresetName = '';
        $this->showSavePreset = false;
        $this->loadPresets();

        $this->dispatch('datatable-preset-saved', name: $name);
    }

    /**
     * Load a saved preset and apply its filters.
     */
    public function applyFilterPreset(string $name): void
    {
        $preset = collect($this->filterPresets)->firstWhere('name', $name);

        if (!$preset) {
            return;
        }

        $this->filterBy = $preset['filterBy'] ?? [''];
        $this->query = $preset['query'] ?? [''];
        $this->sortField = $preset['sortField'] ?? $this->defaultSortField;
        $this->sortDirection = $preset['sortDirection'] ?? $this->defaultSortDirection;
        $this->search = $preset['search'] ?? '';
        $this->filterDataSearch = !empty(array_filter($this->query));
        $this->filter = true;

        $this->resetPage();
    }

    /**
     * Delete a saved preset.
     */
    public function deleteFilterPreset(string $name): void
    {
        $driver = config('livewire-datatable.saved_filters.driver', 'session');

        if ($driver === 'database') {
            $this->deletePresetFromDatabase($name);
        } else {
            $this->deletePresetFromSession($name);
        }

        $this->loadPresets();
    }

    /**
     * Load all presets for current user/model.
     */
    protected function loadPresets(): void
    {
        $driver = config('livewire-datatable.saved_filters.driver', 'session');

        $this->filterPresets = $driver === 'database'
            ? $this->loadPresetsFromDatabase()
            : $this->loadPresetsFromSession();
    }

    // --- Session driver ---

    protected function sessionKey(): string
    {
        $modelKey = class_basename($this->model ?? 'datatable');
        return "datatable_presets_{$modelKey}";
    }

    protected function savePresetToSession(string $name, array $preset): void
    {
        $presets = session($this->sessionKey(), []);
        $presets[$name] = $preset;
        session([$this->sessionKey() => $presets]);
    }

    protected function loadPresetsFromSession(): array
    {
        return array_values(session($this->sessionKey(), []));
    }

    protected function deletePresetFromSession(string $name): void
    {
        $presets = session($this->sessionKey(), []);
        unset($presets[$name]);
        session([$this->sessionKey() => $presets]);
    }

    // --- Database driver ---

    protected function savePresetToDatabase(array $preset): void
    {
        \DB::table('datatable_filter_presets')->updateOrInsert(
            [
                'user_id' => auth()->id(),
                'model' => $this->model,
                'name' => $preset['name'],
            ],
            [
                'filters' => json_encode($preset),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    protected function loadPresetsFromDatabase(): array
    {
        return \DB::table('datatable_filter_presets')
            ->where('user_id', auth()->id())
            ->where('model', $this->model)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($row) => json_decode($row->filters, true))
            ->toArray();
    }

    protected function deletePresetFromDatabase(string $name): void
    {
        \DB::table('datatable_filter_presets')
            ->where('user_id', auth()->id())
            ->where('model', $this->model)
            ->where('name', $name)
            ->delete();
    }
}
