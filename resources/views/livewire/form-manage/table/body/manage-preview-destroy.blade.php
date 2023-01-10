<b>Anteprima:</b>
<div class="container-fluid">
    @foreach (collect($cols) as $_i)
        @php
            data_set($_i, 'output', \Nabre\Repositories\FormTwo\Field::STATIC);
            data_set($_i, 'value', data_get($_row, data_get($_i, 'variable')));
        @endphp
        <div class="row border-top pt-1 mt-2">
            <div class="col-3">
                {{ data_get($_i, 'label') }}:
            </div>
            <div class="col">
                @include('Nabre::livewire.form-manage.item')
            </div>
        </div>
    @endforeach
</div>
