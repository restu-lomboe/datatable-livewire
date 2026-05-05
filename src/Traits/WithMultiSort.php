<?php

namespace Developerawam\LivewireDatatable\Traits;

/**
 * Trait WithMultiSort
 *
 * FIX: bootWithMultiSort() dihapus.
 * $multiSort adalah Livewire public property — nilainya dipertahankan
 * antar request. boot() yang menulis ulang dari config akan reset state
 * yang sudah di-set saat mount().
 *
 * $sortStack juga public property, otomatis dipertahankan Livewire.
 */
trait WithMultiSort
{
    /**
     * Stack of sort rules: [['field' => 'name', 'direction' => 'asc'], ...]
     * Dipertahankan Livewire antar request.
     */
    public array $sortStack = [];

    /** Whether multi-sort is enabled — diset saat mount(), dipertahankan Livewire */
    public bool $multiSort = false;

    /**
     * Handle column header click.
     *
     * @param string $field      Column key
     * @param bool   $addToStack True saat Ctrl ditekan (dikirim dari JS)
     */
    public function sortByMulti(string $field, bool $addToStack = false): void
    {
        if (!$this->multiSort || !$addToStack) {
            // Normal single-column sort — reset stack, delegate ke sortBy()
            $this->sortStack = [];
            $this->sortBy($field);
            return;
        }

        // Cari apakah field sudah ada di stack
        $existingIndex = null;
        foreach ($this->sortStack as $i => $entry) {
            if ($entry['field'] === $field) {
                $existingIndex = $i;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Toggle direction untuk field ini
            $current = $this->sortStack[$existingIndex]['direction'];
            $this->sortStack[$existingIndex]['direction'] = $current === 'asc' ? 'desc' : 'asc';
        } else {
            // Tambah ke stack (max 3 level)
            if (count($this->sortStack) >= 3) {
                array_shift($this->sortStack);
            }
            $this->sortStack[] = ['field' => $field, 'direction' => 'asc'];
        }

        // Sync primary sort field agar kompatibel dengan kode existing
        if (!empty($this->sortStack)) {
            $primary = $this->sortStack[0];
            $this->sortField = $primary['field'];
            $this->sortDirection = $primary['direction'];
        }

        $this->resetPage();
    }

    public function removeSortEntry(string $field): void
    {
        $this->sortStack = array_values(
            array_filter($this->sortStack, fn ($e) => $e['field'] !== $field)
        );

        if (!empty($this->sortStack)) {
            $this->sortField = $this->sortStack[0]['field'];
            $this->sortDirection = $this->sortStack[0]['direction'];
        } else {
            $this->sortField = $this->defaultSortField;
            $this->sortDirection = $this->defaultSortDirection;
        }

        $this->resetPage();
    }

    public function applyMultiSort(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        if (empty($this->sortStack)) {
            return $query;
        }

        foreach ($this->sortStack as $entry) {
            $query->orderBy($entry['field'], $entry['direction']);
        }

        return $query;
    }

    /**
     * Dapatkan nomor prioritas sort untuk kolom tertentu (1, 2, 3 atau null).
     */
    public function getSortPriority(string $field): ?int
    {
        if (!$this->multiSort || empty($this->sortStack)) {
            return null;
        }

        foreach ($this->sortStack as $i => $entry) {
            if ($entry['field'] === $field) {
                return $i + 1;
            }
        }

        return null;
    }
}
