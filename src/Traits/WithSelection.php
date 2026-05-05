<?php

namespace Developerawam\LivewireDatatable\Traits;

/**
 * Trait WithSelection — Row checkbox selection.
 *
 * FIX: bootWithSelection() dihapus.
 * $selectable adalah Livewire public property — nilainya otomatis dipertahankan
 * antar request oleh Livewire state hydration.
 * boot() yang menulis ulang $selectable dari config akan menimpa nilai
 * yang di-set saat mount(), menyebabkan feature hilang setelah reload.
 */
trait WithSelection
{
    /** Array of selected primary key values */
    public array $selectedIds = [];

    /** Whether all visible rows are selected */
    public bool $selectAll = false;

    /** Whether selection feature is enabled — diset saat mount(), dipertahankan Livewire */
    public bool $selectable = false;

    public function toggleSelect(int|string $id): void
    {
        if (in_array($id, $this->selectedIds)) {
            $this->selectedIds = array_values(array_filter(
                $this->selectedIds,
                fn ($v) => $v !== $id
            ));
            $this->selectAll = false;
        } else {
            $this->selectedIds[] = $id;
        }

        $this->dispatch('datatable-selection-changed', ids: $this->selectedIds);
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedIds = [];
            $this->selectAll = false;
        } else {
            $currentPageIds = $this->getCurrentPageIds();
            $this->selectedIds = array_values(array_unique(
                array_merge($this->selectedIds, $currentPageIds)
            ));
            $this->selectAll = true;
        }

        $this->dispatch('datatable-selection-changed', ids: $this->selectedIds);
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('datatable-selection-changed', ids: []);
    }

    protected function getCurrentPageIds(): array
    {
        if (!$this->model) {
            return [];
        }

        $keyName = (new $this->model)->getKeyName();
        $results = $this->getQuery;

        return collect($results->items())
            ->pluck($keyName)
            ->values()
            ->toArray();
    }

    public function isSelected(int|string $id): bool
    {
        return in_array($id, $this->selectedIds);
    }

    public function getSelectedCountProperty(): int
    {
        return count($this->selectedIds);
    }
}
