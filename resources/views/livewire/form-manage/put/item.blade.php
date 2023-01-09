@php
    $_name = data_get($_i, 'set.options')['wire:model.defer'] ?? null;
@endphp
@if (count($errors) > 0)
    @if ($errors->has($_name))
        @php
            $_ins = $errors->getMessages($_name);
        @endphp
    @else
        @php
            $_ins = false;
        @endphp
    @endif
    @php
        data_set($_i, 'errors_print',$_ins);
    @endphp
@endif
{!! \Nabre\Repositories\FormTwo\Field::generate($_i) !!}
