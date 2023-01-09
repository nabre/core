@php
    $_list = collect(data_get($this, data_get($_i, 'set.options')['wire:model.defer']))->keys();
    $_moveBool = (bool) $_list->count();
@endphp
<div class="list-group">
    @foreach ($_list as $_num)
        <li class="list-group-item">
            <div class="row">
                <div class="col-auto">MOVE</div>
                <div class="col">@include('Nabre::livewire.form-manage.embed.one')</div>
                <div class="col-auto">del</div>
            </div>
        </li>
    @endforeach
    <button type="button" class="text-center list-group-item list-group-item-action list-group-item-secondary"
        title='Aggiungi' wire:click="embedItAdd('$param')"><i class="fa-regular fa-square-plus"></i></button>
</div>
@php
    $_num = null;
@endphp
