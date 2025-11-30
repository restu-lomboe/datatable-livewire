# Number Column ("no") Behavior Fix

## Issue

When sorting the datatable, the "no" (row number) column was behaving incorrectly:

- It was trying to reverse numbering based on sort direction
- This caused confusion when sorting by different fields
- The numbering wasn't consistent regardless of what field was being sorted

## Solution

The "no" column now **always displays sequential numbering** (1, 2, 3...) based on the current page and row position, regardless of:

- Which field is being sorted
- The sort direction (asc/desc)
- Whether a default sort is applied

## Behavior

### Example 1: First Load (Default Order)

```
When defaultSortField = 'created_at', defaultSortDirection = 'desc'

Page 1 displays:
No | Title         | Created At
1  | Article A     | 2024-01-15
2  | Article B     | 2024-01-14
3  | Article C     | 2024-01-13
```

### Example 2: Click Sort by Title (Ascending)

```
User clicks "Title" column to sort

Page 1 displays:
No | Title         | Created At
1  | Article A     | 2024-01-10
2  | Article B     | 2024-01-15
3  | Article C     | 2024-01-13

Notice: "No" column still shows 1, 2, 3...
It does NOT reverse or change based on sort direction
```

### Example 3: Next Page

```
If perPage = 10, moving to page 2 shows:

No | Title         | Created At
11 | Article K     | 2024-01-09
12 | Article L     | 2024-01-08
13 | Article M     | 2024-01-07

Row numbering continues from 11 based on pagination
```

## Technical Details

### Before (Old Logic):

```blade
@if ($sortDirection === 'desc')
    {{ sequential numbering }}
@elseif ($sortDirection === 'asc')
    {{ reverse numbering based on total }}
@endif
```

❌ Problem: "no" column behavior changed based on sort direction

### After (New Logic):

```blade
@if ($key === 'no')
    {{ $loop->parent->iteration + ($this->getQuery->currentPage() - 1) * $this->getQuery->perPage() }}
@endif
```

✅ Solution: Always use sequential numbering based on page position

## Files Modified

- `resources/views/datatable.blade.php` - Simplified "no" column rendering logic

## Impact

- ✅ Consistent row numbering regardless of sort field or direction
- ✅ Works correctly with default order by feature
- ✅ Works correctly with pagination
- ✅ Cleaner, more maintainable code
- ✅ No performance impact

## Example Usage

```blade
<livewire:livewire-datatable
    :model="$model"
    :columns="[
        'no' => '#',
        'title' => 'Title',
        'user.name' => 'Author',
        'created_at' => 'Created'
    ]"
    :defaultSortField="'created_at'"
    :defaultSortDirection="'desc'"
/>
```

When user interacts with this table:

1. **Initial load**: Sorted by created_at DESC, "no" shows 1,2,3...
2. **Click "Title" header**: Sorted by title ASC, "no" still shows 1,2,3...
3. **Click "Author" header**: Sorted by user.name ASC, "no" still shows 1,2,3...
4. **Move to page 2**: "no" shows 11,12,13... (assuming 10 per page)

The "no" column is completely independent of the current sort field/direction.
