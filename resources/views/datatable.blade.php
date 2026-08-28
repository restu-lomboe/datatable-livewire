{{-- Legacy view — proxies to configured template (tailwind/bootstrap) to avoid drift. --}}
@include('livewire-datatable::templates.'.config('livewire-datatable.template', 'tailwind').'.datatable')
