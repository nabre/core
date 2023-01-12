@php
    $_wireModel = data_get($_i, \Nabre\Repositories\FormTwo\FormConst::OPTIONS_WIREMODEL);
    $_list = collect(data_get($this, $_wireModel))->keys();
    $_moveBool = (bool) $_list->count();
    $_embedStr = collect(explode('.', $_wireModel))
        ->skip(1)
        ->implode('.');
@endphp
<div class="list-group">
    @if (count($_list))
        @foreach ($_list as $_num)
            @php
                $_hrBool = null;
            @endphp
            <li class="list-group-item">
                <div class="row">
                    @if (count($_list) > 1)
                        <div class="col-auto">MOVE</div>
                    @endif
                    <div class="col"> @include('Nabre::livewire.form-manage.put.print')</div>
                    @if (!in_array('required', data_get($_i, 'set.request.' . $method, [])) || count($_list)>1)
                    <div class="col-auto">
                        <button type="button" class="btn btn-warning btn-sm h-100" title="Elimina"
                            wire:click="embedItRemove('{{ $_embedStr }}',{{ $_num }})">
                            <i class="fa-regular fa-square-minus"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </li>
        @endforeach
    @else
        <li class="list-group-item">
            Non ci sono elementi salvati. Clicca qui sotto per aggiungere.
        </li>
    @endif

    <button type="button" class="text-center list-group-item list-group-item-action list-group-item-secondary"
        title='Aggiungi' wire:click="embedItAdd('{{ $_embedStr }}')"><i class="fa-regular fa-square-plus"></i>
    </button>
</div>
@php
    $_num = null;
@endphp
