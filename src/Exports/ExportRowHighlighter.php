<?php

namespace Developerawam\LivewireDatatable\Exports;

use Illuminate\Support\Str;

class ExportRowHighlighter
{
    /**
     * @var array<int, array{match: string, rules: array<int, array{column: string, operator: string, value: mixed}>, background: string}>
     */
    protected array $groups = [];

    public function __construct(array $conditions = [], array $styles = [])
    {
        if ($conditions === [] || $styles === []) {
            return;
        }

        $flatStyle = $this->flatStyle($styles);
        $styleGroups = $flatStyle === null ? $styles : [];

        foreach ($this->normalizeGroups($conditions) as $name => $definition) {
            $group = $this->normalizeGroup($definition);

            if ($group['rules'] === []) {
                continue;
            }

            $background = $flatStyle ?? $this->styleForGroup($styleGroups, $name);

            if ($background === null) {
                continue;
            }

            $group['background'] = $background;

            $this->groups[] = $group;
        }
    }

    public function backgroundColorFor(mixed $row): ?string
    {
        foreach ($this->groups as $group) {
            if ($this->matchesGroup($row, $group)) {
                return $group['background'];
            }
        }

        return null;
    }

    public function hasConditions(): bool
    {
        return $this->groups !== [];
    }

    /**
     * @return array<int|string, array<mixed>>
     */
    protected function normalizeGroups(array $conditions): array
    {
        if ($this->looksLikeGroup($conditions)) {
            return [0 => $conditions];
        }

        if ($this->looksLikeRule($conditions)) {
            return [0 => ['rules' => [$conditions]]];
        }

        if ($this->looksLikeRuleList($conditions)) {
            return [0 => ['rules' => $conditions]];
        }

        $groups = [];

        foreach ($conditions as $name => $definition) {
            if (! is_array($definition)) {
                continue;
            }

            if (
                $this->looksLikeGroup($definition)
                || $this->looksLikeRule($definition)
                || $this->looksLikeRuleList($definition)
            ) {
                $groups[$name] = $definition;
            }
        }

        return $groups;
    }

    /**
     * @return array{match: string, rules: array<int, array{column: string, operator: string, value: mixed}>}
     */
    protected function normalizeGroup(array $definition): array
    {
        if ($this->looksLikeRule($definition)) {
            $definition = ['rules' => [$definition]];
        } elseif ($this->looksLikeRuleList($definition)) {
            $definition = ['rules' => $definition];
        }

        $rules = $definition['rules'] ?? [];

        if (! is_array($rules)) {
            $rules = [];
        }

        $normalizedRules = [];

        foreach ($rules as $rule) {
            if (! is_array($rule)) {
                continue;
            }

            $column = $rule['column'] ?? $rule['field'] ?? $rule['key'] ?? null;

            if (! is_string($column) || trim($column) === '') {
                continue;
            }

            $normalizedRules[] = [
                'column' => trim($column),
                'operator' => $this->normalizeOperator($rule['operator'] ?? '='),
                'value' => array_key_exists('value', $rule) ? $rule['value'] : null,
            ];
        }

        return [
            'match' => $this->normalizeMatch($definition['match'] ?? 'any'),
            'rules' => $normalizedRules,
        ];
    }

    protected function looksLikeGroup(array $definition): bool
    {
        return array_key_exists('rules', $definition) || array_key_exists('match', $definition);
    }

    protected function looksLikeRule(array $definition): bool
    {
        return array_key_exists('column', $definition)
            || array_key_exists('field', $definition)
            || array_key_exists('key', $definition);
    }

    protected function looksLikeRuleList(array $definition): bool
    {
        if ($definition === [] || ! array_is_list($definition)) {
            return false;
        }

        foreach ($definition as $rule) {
            if (! is_array($rule) || ! $this->looksLikeRule($rule)) {
                return false;
            }
        }

        return true;
    }

    protected function normalizeMatch(mixed $match): string
    {
        return in_array(Str::lower(trim((string) $match)), ['all', 'and', 'every'], true) ? 'all' : 'any';
    }

    protected function normalizeOperator(mixed $operator): string
    {
        $operator = Str::lower(trim((string) $operator));

        return match ($operator) {
            '', '=', '==', 'eq', 'equals', 'is' => '=',
            '!=', '<>', 'ne', 'not_equals', 'not equals' => '!=',
            'gt' => '>',
            'gte' => '>=',
            'lt' => '<',
            'lte' => '<=',
            'contain' => 'contains',
            'not contain', 'not_contains' => 'not_contains',
            'starts', 'startswith', 'starts with' => 'starts_with',
            'ends', 'endswith', 'ends with' => 'ends_with',
            'not in' => 'not_in',
            'not between' => 'not_between',
            'not empty' => 'not_empty',
            'is_null', 'is null' => 'null',
            'not_null', 'not null', 'is_not_null', 'is not null' => 'not_null',
            default => $operator,
        };
    }

    protected function flatStyle(array $styles): ?string
    {
        if ($this->isStyleDefinition($styles)) {
            return $this->normalizeStyle($styles);
        }

        if (array_is_list($styles) && count($styles) === 1) {
            $style = reset($styles);

            if (is_string($style)) {
                return $this->normalizeColor($style);
            }

            if (is_array($style)) {
                return $this->normalizeStyle($style);
            }
        }

        return null;
    }

    protected function isStyleDefinition(array $style): bool
    {
        foreach (['background', 'background_color', 'background-color', 'bg', 'fill', 'fill_color'] as $key) {
            if (array_key_exists($key, $style)) {
                return true;
            }
        }

        return false;
    }

    protected function styleForGroup(array $styles, int|string $name): ?string
    {
        if (array_key_exists($name, $styles)) {
            $background = $this->normalizeStyle($styles[$name]);

            if ($background !== null) {
                return $background;
            }
        }

        if (array_key_exists('default', $styles)) {
            $background = $this->normalizeStyle($styles['default']);

            if ($background !== null) {
                return $background;
            }
        }

        if ($name === 0 && count($styles) === 1) {
            return $this->normalizeStyle(reset($styles));
        }

        return null;
    }

    protected function normalizeStyle(mixed $style): ?string
    {
        if (is_string($style)) {
            return $this->normalizeColor($style);
        }

        if (! is_array($style)) {
            return null;
        }

        $background = $style['background']
            ?? $style['background_color']
            ?? $style['background-color']
            ?? $style['bg']
            ?? $style['fill']
            ?? $style['fill_color']
            ?? null;

        return is_string($background) ? $this->normalizeColor($background) : null;
    }

    protected function normalizeColor(string $color): ?string
    {
        $color = ltrim(trim($color), '#');

        if (preg_match('/^[0-9a-f]{3}$/i', $color) === 1) {
            $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
        } elseif (preg_match('/^[0-9a-f]{8}$/i', $color) === 1) {
            // Excel ARGB input; keep the RGB part for consistency.
            $color = substr($color, 2);
        }

        return preg_match('/^[0-9a-f]{6}$/i', $color) === 1 ? '#'.Str::upper($color) : null;
    }

    protected function matchesGroup(mixed $row, array $group): bool
    {
        foreach ($group['rules'] as $rule) {
            $matches = $this->matchesRule($row, $rule);

            if ($group['match'] === 'all' && ! $matches) {
                return false;
            }

            if ($group['match'] === 'any' && $matches) {
                return true;
            }
        }

        return $group['match'] === 'all';
    }

    protected function matchesRule(mixed $row, array $rule): bool
    {
        $actual = data_get($row, $rule['column']);
        $expected = $rule['value'];

        return match ($rule['operator']) {
            '=' => $this->valuesEqual($actual, $expected),
            '!=' => ! $this->valuesEqual($actual, $expected),
            '>' => $this->compareValues($actual, $expected) > 0,
            '>=' => $this->compareValues($actual, $expected) >= 0,
            '<' => $this->compareValues($actual, $expected) < 0,
            '<=' => $this->compareValues($actual, $expected) <= 0,
            'contains' => $this->contains($actual, $expected),
            'not_contains' => ! $this->contains($actual, $expected),
            'starts_with' => Str::startsWith(Str::lower((string) $actual), Str::lower((string) $expected)),
            'ends_with' => Str::endsWith(Str::lower((string) $actual), Str::lower((string) $expected)),
            'in' => $this->isIn($actual, $expected),
            'not_in' => ! $this->isIn($actual, $expected),
            'between' => $this->isBetween($actual, $expected),
            'not_between' => ! $this->isBetween($actual, $expected),
            'empty' => blank($actual),
            'not_empty' => ! blank($actual),
            'null' => $actual === null,
            'not_null' => $actual !== null,
            default => false,
        };
    }

    protected function valuesEqual(mixed $actual, mixed $expected): bool
    {
        if ($expected === null) {
            return $actual === null || $actual === '';
        }

        if ($actual === null) {
            return false;
        }

        if (is_array($actual) || is_array($expected)) {
            return $actual === $expected;
        }

        $actualBoolean = $this->booleanValue($actual);
        $expectedBoolean = $this->booleanValue($expected);

        if ($actualBoolean !== null && $expectedBoolean !== null) {
            return $actualBoolean === $expectedBoolean;
        }

        if (is_numeric($actual) && is_numeric($expected)) {
            return (float) $actual === (float) $expected;
        }

        return strcasecmp(trim((string) $actual), trim((string) $expected)) === 0;
    }

    protected function booleanValue(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            $number = (float) $value;

            if ($number === 1.0) {
                return true;
            }

            if ($number === 0.0) {
                return false;
            }

            return null;
        }

        if (! is_string($value)) {
            return null;
        }

        return match (Str::lower(trim($value))) {
            '1', 'true', 'yes', 'y', 'on' => true,
            '0', 'false', 'no', 'n', 'off' => false,
            default => null,
        };
    }

    protected function compareValues(mixed $actual, mixed $expected): int
    {
        if (is_array($actual) || is_array($expected)) {
            return 0;
        }

        if (is_numeric($actual) && is_numeric($expected)) {
            return (float) $actual <=> (float) $expected;
        }

        return strcmp((string) $actual, (string) $expected);
    }

    protected function contains(mixed $actual, mixed $expected): bool
    {
        if (is_array($actual)) {
            foreach ($actual as $value) {
                if ($this->valuesEqual($value, $expected)) {
                    return true;
                }
            }

            return false;
        }

        return Str::contains(Str::lower((string) $actual), Str::lower((string) $expected));
    }

    protected function isIn(mixed $actual, mixed $expected): bool
    {
        $values = is_array($expected) ? $expected : [$expected];

        foreach ($values as $value) {
            if ($this->valuesEqual($actual, $value)) {
                return true;
            }
        }

        return false;
    }

    protected function isBetween(mixed $actual, mixed $expected): bool
    {
        if (! is_array($expected) || ! is_numeric($actual)) {
            return false;
        }

        $values = array_values($expected);

        if (count($values) < 2 || ! is_numeric($values[0]) || ! is_numeric($values[1])) {
            return false;
        }

        return (float) $actual >= (float) $values[0] && (float) $actual <= (float) $values[1];
    }
}
