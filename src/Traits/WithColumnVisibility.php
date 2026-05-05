<?php

namespace Developerawam\LivewireDatatable\Traits;

/**
 * Trait WithColumnVisibility
 *
 * FIX: bootWithColumnVisibility() dihapus.
 * $columnVisibility adalah Livewire public property — nilainya dipertahankan
 * antar request. boot() yang menulis ulang dari config akan reset state
 * yang sudah di-set saat mount(), menyebabkan feature hilang setelah setiap
 * Livewire request (sort, search, pagination, dll).
 *
 * $hiddenColumns juga public property, artinya Livewire sudah menyimpan
 * state-nya — tidak perlu session restore di setiap boot.
 */
trait WithColumnVisibility
{
    /** Hidden column keys — dipertahankan Livewire antar request */
    public array $hiddenColumns = [];

    /** Whether column visibility toggle UI is shown */
    public bool $showColumnToggler = false;

    /** Whether column visibility feature is enabled — diset saat mount(), dipertahankan Livewire */
    public bool $columnVisibility = false;

    public function toggleColumn(string $column): void
    {
        if (in_array($column, $this->hiddenColumns)) {
            $this->hiddenColumns = array_values(
                array_filter($this->hiddenColumns, fn ($c) => $c !== $column)
            );
        } else {
            // Jangan sembunyikan semua kolom — minimal 1 harus terlihat
            $visibleCount = count($this->columns) - count($this->hiddenColumns);
            if ($visibleCount <= 1) {
                return;
            }
            $this->hiddenColumns[] = $column;
        }
        // Tidak perlu session — $hiddenColumns sudah dipertahankan Livewire
    }

    public function showAllColumns(): void
    {
        $this->hiddenColumns = [];
    }

    public function isColumnVisible(string $column): bool
    {
        return !in_array($column, $this->hiddenColumns);
    }

    public function getVisibleColumns(): array
    {
        if (empty($this->hiddenColumns)) {
            return $this->columns;
        }

        return array_filter(
            $this->columns,
            fn ($_, $key) => !in_array($key, $this->hiddenColumns),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
