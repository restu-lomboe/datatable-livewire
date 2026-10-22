<input type="checkbox" wire:click="toggleRow({{ $rowKey }})"
    wire:key="selection-row-{{ $rowKey }}-{{ $checked ? 'checked' : 'unchecked' }}"
    wire:loading.attr="disabled" @checked($checked) aria-label="{{ $label }}: {{ $rowKey }}"
    data-class="selection_checkbox" @class([$class])>
