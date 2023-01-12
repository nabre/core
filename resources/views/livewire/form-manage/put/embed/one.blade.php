@php
    $_hrBool = null;
    $_wireModel = data_get($_i, \Nabre\Repositories\FormTwo\FormConst::OPTIONS_WIREMODEL);
    $_embedStr = collect(explode('.', $_wireModel))
        ->skip(1)
        ->implode('.');

    //  dd(get_defined_vars());

@endphp
@if (is_null(data_get($this, $_wireModel)))
    <div class="list-group">
        <button type="button" class="text-center list-group-item list-group-item-action list-group-item-secondary"
            title='Aggiungi' wire:click="embedItAdd('{{ $_embedStr }}')"><i class="fa-regular fa-square-plus"></i>
        </button>
    </div>
@else
    @if (in_array('nullable', data_get($_i, 'set.request.' . $method, [])))
        <div class="row">
            <div class="col">
                @include('Nabre::livewire.form-manage.put.print')
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-warning btn-sm h-100" title="Elimina"
                    wire:click="embedItRemove('{{ $_embedStr }}')">
                    <i class="fa-regular fa-square-minus"></i>
                </button>
            </div>
        </div>
    @else
        @include('Nabre::livewire.form-manage.put.print')
    @endif
@endif
