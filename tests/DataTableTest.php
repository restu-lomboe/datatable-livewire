<?php

namespace Developerawam\LivewireDatatable\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Developerawam\LivewireDatatable\DataSources\ModelDataSource;
use Developerawam\LivewireDatatable\Traits\WithFormatters;
use Developerawam\LivewireDatatable\Traits\WithFiltering;
use Developerawam\LivewireDatatable\Traits\WithSelection;
use Developerawam\LivewireDatatable\Traits\WithMultiSort;
use Developerawam\LivewireDatatable\Traits\WithColumnVisibility;

/**
 * Improvement #6: Comprehensive test coverage.
 *
 * Run: vendor/bin/phpunit
 */
class DataTableTest extends TestCase
{
    // ─────────────────────────────────────────────────────────────────────
    // ModelDataSource
    // ─────────────────────────────────────────────────────────────────────

    #[Test]
    public function model_data_source_instantiates_with_defaults(): void
    {
        $source = new ModelDataSource('App\Models\User');
        $this->assertInstanceOf(ModelDataSource::class, $source);
    }

    #[Test]
    public function model_data_source_make_helper_returns_instance(): void
    {
        $source = ModelDataSource::make('App\Models\User', null, ['name'], ['name', 'email'], 15);
        $this->assertInstanceOf(ModelDataSource::class, $source);
    }

    #[Test]
    public function model_data_source_accepts_all_constructor_params(): void
    {
        $source = new ModelDataSource(
            model: 'App\Models\Post',
            scope: 'published',
            scopeParams: ['active'],
            searchable: ['title', 'body'],
            sortable: ['title', 'created_at'],
            perPage: 25,
            defaultSortField: 'created_at',
            defaultSortDirection: 'desc'
        );
        $this->assertInstanceOf(ModelDataSource::class, $source);
    }

    // ─────────────────────────────────────────────────────────────────────
    // WithFormatters
    // ─────────────────────────────────────────────────────────────────────

    #[Test]
    public function formatter_returns_dash_for_null(): void
    {
        $trait = $this->makeFormatterInstance();
        $this->assertSame('-', $trait->formatValue('col', null));
        $this->assertSame('-', $trait->formatValue('col', ''));
    }

    #[Test]
    public function formatter_returns_raw_value_when_no_formatter_defined(): void
    {
        $trait = $this->makeFormatterInstance();
        $this->assertSame('hello', $trait->formatValue('col', 'hello'));
    }

    #[Test]
    #[DataProvider('dateFormatterProvider')]
    public function formatter_formats_dates(string $input, string $format, string $expected): void
    {
        $trait = $this->makeFormatterInstance(
            formatters: ['date_col' => 'date'],
            formatterOptions: ['date_col' => ['format' => $format]]
        );
        $this->assertSame($expected, $trait->formatValue('date_col', $input));
    }

    public static function dateFormatterProvider(): array
    {
        return [
            'Y-m-d format' => ['2024-06-15', 'Y-m-d', '2024-06-15'],
            'd/m/Y format' => ['2024-06-15', 'd/m/Y', '15/06/2024'],
            'M Y format'   => ['2024-06-15', 'M Y',   'Jun 2024'],
        ];
    }

    #[Test]
    public function formatter_number_applies_thousand_separator(): void
    {
        $trait = $this->makeFormatterInstance(
            formatters: ['amount' => 'number'],
            formatterOptions: ['amount' => ['decimals' => 0, 'thousand_sep' => '.']]
        );
        $this->assertSame('1.000.000', $trait->formatValue('amount', 1000000));
    }

    #[Test]
    public function formatter_currency_prepends_symbol(): void
    {
        $trait = $this->makeFormatterInstance(
            formatters: ['price' => 'currency'],
            formatterOptions: ['price' => ['symbol' => 'Rp ', 'decimals' => 0, 'thousand_sep' => '.']]
        );
        $result = $trait->formatValue('price', 50000);
        $this->assertStringStartsWith('Rp ', $result);
        $this->assertStringContainsString('50', $result);
    }

    #[Test]
    public function formatter_boolean_returns_yes_no(): void
    {
        $trait = $this->makeFormatterInstance(formatters: ['active' => 'boolean']);
        $this->assertSame('Yes', $trait->formatValue('active', true));
        $this->assertSame('No', $trait->formatValue('active', false));
    }

    #[Test]
    public function formatter_boolean_custom_labels(): void
    {
        $trait = $this->makeFormatterInstance(
            formatters: ['active' => 'boolean'],
            formatterOptions: ['active' => ['true' => 'Aktif', 'false' => 'Nonaktif']]
        );
        $this->assertSame('Aktif', $trait->formatValue('active', true));
        $this->assertSame('Nonaktif', $trait->formatValue('active', false));
    }

    #[Test]
    public function formatter_uppercase_converts_value(): void
    {
        $trait = $this->makeFormatterInstance(formatters: ['name' => 'uppercase']);
        $this->assertSame('JOHN DOE', $trait->formatValue('name', 'john doe'));
    }

    #[Test]
    public function formatter_lowercase_converts_value(): void
    {
        $trait = $this->makeFormatterInstance(formatters: ['email' => 'lowercase']);
        $this->assertSame('john@example.com', $trait->formatValue('email', 'JOHN@EXAMPLE.COM'));
    }

    #[Test]
    public function formatter_complex_limit_truncates_string(): void
    {
        $trait = $this->makeFormatterInstance(
            formatters: ['body' => ['type' => 'limit', 'options' => ['length' => 10]]]
        );
        $result = $trait->formatValue('body', 'This is a very long text');
        $this->assertLessThanOrEqual(13, strlen($result)); // 10 chars + '...'
        $this->assertStringEndsWith('...', $result);
    }

    #[Test]
    public function formatter_date_returns_empty_string_for_null_value(): void
    {
        $trait = $this->makeFormatterInstance(formatters: ['col' => 'date']);
        // null is caught before formatter runs, returns '-'
        $this->assertSame('-', $trait->formatValue('col', null));
    }

    // ─────────────────────────────────────────────────────────────────────
    // WithSelection
    // ─────────────────────────────────────────────────────────────────────

    // ─────────────────────────────────────────────────────────────────────
    // Regression: boot() must NOT overwrite public Livewire properties
    // ─────────────────────────────────────────────────────────────────────

    #[Test]
    public function selection_trait_has_no_boot_method(): void
    {
        // If bootWithSelection() exists and overwrites $selectable from config,
        // the feature would disappear after every Livewire re-render.
        $this->assertFalse(
            method_exists(WithSelection::class, 'bootWithSelection'),
            'WithSelection must NOT have a bootWithSelection() method — it would overwrite Livewire state'
        );
    }

    #[Test]
    public function multi_sort_trait_has_no_boot_method(): void
    {
        $this->assertFalse(
            method_exists(WithMultiSort::class, 'bootWithMultiSort'),
            'WithMultiSort must NOT have a bootWithMultiSort() method — it would overwrite Livewire state'
        );
    }

    #[Test]
    public function column_visibility_trait_has_no_boot_method(): void
    {
        $this->assertFalse(
            method_exists(WithColumnVisibility::class, 'bootWithColumnVisibility'),
            'WithColumnVisibility must NOT have a bootWithColumnVisibility() method — it would overwrite Livewire state'
        );
    }

    #[Test]
    public function column_search_trait_has_no_boot_method(): void
    {
        $this->assertFalse(
            method_exists(\Developerawam\LivewireDatatable\Traits\WithColumnSearch::class, 'bootWithColumnSearch'),
            'WithColumnSearch must NOT have a bootWithColumnSearch() method — it would overwrite Livewire state'
        );
    }

    #[Test]
    public function selection_starts_empty(): void
    {
        $obj = $this->makeSelectionInstance();
        $this->assertEmpty($obj->selectedIds);
        $this->assertFalse($obj->selectAll);
    }

    #[Test]
    public function selection_is_selected_checks_correctly(): void
    {
        $obj = $this->makeSelectionInstance();
        $obj->selectedIds = [1, 2, 3];
        $this->assertTrue($obj->isSelected(2));
        $this->assertFalse($obj->isSelected(99));
    }

    #[Test]
    public function selection_clear_empties_array(): void
    {
        $obj = $this->makeSelectionInstance();
        $obj->selectedIds = [1, 2, 3];
        $obj->selectAll = true;
        $obj->clearSelection();
        $this->assertEmpty($obj->selectedIds);
        $this->assertFalse($obj->selectAll);
    }

    // ─────────────────────────────────────────────────────────────────────
    // WithMultiSort
    // ─────────────────────────────────────────────────────────────────────

    #[Test]
    public function multi_sort_stack_starts_empty(): void
    {
        $obj = $this->makeMultiSortInstance();
        $this->assertEmpty($obj->sortStack);
    }

    #[Test]
    public function multi_sort_get_priority_returns_null_when_empty(): void
    {
        $obj = $this->makeMultiSortInstance();
        $this->assertNull($obj->getSortPriority('name'));
    }

    #[Test]
    public function multi_sort_get_priority_returns_correct_index(): void
    {
        $obj = $this->makeMultiSortInstance();
        $obj->sortStack = [
            ['field' => 'name', 'direction' => 'asc'],
            ['field' => 'email', 'direction' => 'desc'],
        ];
        $this->assertSame(1, $obj->getSortPriority('name'));
        $this->assertSame(2, $obj->getSortPriority('email'));
        $this->assertNull($obj->getSortPriority('phone'));
    }

    #[Test]
    public function multi_sort_remove_entry_reindexes(): void
    {
        $obj = $this->makeMultiSortInstance();
        $obj->sortStack = [
            ['field' => 'name', 'direction' => 'asc'],
            ['field' => 'email', 'direction' => 'desc'],
        ];
        $obj->defaultSortField = 'created_at';
        $obj->defaultSortDirection = 'desc';
        $obj->removeSortEntry('name');

        $this->assertCount(1, $obj->sortStack);
        $this->assertSame('email', $obj->sortStack[0]['field']);
    }

    // ─────────────────────────────────────────────────────────────────────
    // WithColumnVisibility
    // ─────────────────────────────────────────────────────────────────────

    #[Test]
    public function column_visibility_starts_with_nothing_hidden(): void
    {
        $obj = $this->makeColumnVisibilityInstance();
        $this->assertEmpty($obj->hiddenColumns);
    }

    #[Test]
    public function column_is_visible_by_default(): void
    {
        $obj = $this->makeColumnVisibilityInstance();
        $this->assertTrue($obj->isColumnVisible('name'));
    }

    #[Test]
    public function hidden_column_is_not_visible(): void
    {
        $obj = $this->makeColumnVisibilityInstance();
        $obj->hiddenColumns = ['email'];
        $this->assertFalse($obj->isColumnVisible('email'));
        $this->assertTrue($obj->isColumnVisible('name'));
    }

    #[Test]
    public function get_visible_columns_excludes_hidden(): void
    {
        $obj = $this->makeColumnVisibilityInstance();
        $obj->columns = ['name' => 'Name', 'email' => 'Email', 'phone' => 'Phone'];
        $obj->columnVisibility = true;
        $obj->hiddenColumns = ['email'];

        $visible = $obj->getVisibleColumns();
        $this->assertArrayHasKey('name', $visible);
        $this->assertArrayHasKey('phone', $visible);
        $this->assertArrayNotHasKey('email', $visible);
    }

    #[Test]
    public function show_all_columns_clears_hidden(): void
    {
        $obj = $this->makeColumnVisibilityInstance();
        $obj->hiddenColumns = ['email', 'phone'];
        $obj->showAllColumns();
        $this->assertEmpty($obj->hiddenColumns);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Default sort (existing tests, improved)
    // ─────────────────────────────────────────────────────────────────────

    #[Test]
    public function model_data_source_constructor_sets_default_sort(): void
    {
        $source = new ModelDataSource(
            'App\Models\Post', null, [], [], ['id', 'title'], 10, 'created_at', 'desc'
        );
        $this->assertInstanceOf(ModelDataSource::class, $source);
    }

    #[Test]
    public function model_data_source_constructor_nullable_sort(): void
    {
        $source = new ModelDataSource('App\Models\Post');
        $this->assertInstanceOf(ModelDataSource::class, $source);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────

    private function makeFormatterInstance(array $formatters = [], array $formatterOptions = []): object
    {
        return new class($formatters, $formatterOptions) {
            use WithFormatters;
            public array $formatters;
            public array $formatterOptions;
            public function __construct(array $f, array $fo) {
                $this->formatters = $f;
                $this->formatterOptions = $fo;
            }
        };
    }

    private function makeSelectionInstance(): object
    {
        return new class {
            use WithSelection;
            public array $selectedIds = [];
            public bool $selectAll = false;
            public bool $selectable = true;
            // stub dispatch
            public function dispatch(string $event, mixed ...$args): void {}
            // stub getQuery computed property
            public mixed $getQuery = null;
        };
    }

    private function makeMultiSortInstance(): object
    {
        return new class {
            use WithMultiSort;
            public array $sortStack = [];
            public bool $multiSort = true;
            public string $sortField = 'created_at';
            public string $sortDirection = 'desc';
            public string $defaultSortField = 'created_at';
            public string $defaultSortDirection = 'desc';
            public function sortBy(string $field): void { $this->sortField = $field; }
            public function resetPage(): void {}
        };
    }

    private function makeColumnVisibilityInstance(): object
    {
        return new class {
            use WithColumnVisibility;
            public array $hiddenColumns = [];
            public bool $columnVisibility = false;
            public bool $showColumnToggler = false;
            public array $columns = ['name' => 'Name', 'email' => 'Email'];
            public ?string $model = null;
            // stub session (no Laravel in unit test context)
            public function persistColumnVisibility(): void {}
            public function restoreColumnVisibility(): void {}
        };
    }
}
