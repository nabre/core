<div class="row">
    @include('Nabre::livewire.form-manage.title')
</div>
<div class="table-responsive">
    <table class="table table-sm w-auto">
        <tr>
            @foreach (collect($cols)->pluck('label') as $_h)
                <th>{{ $_h }}</th>
            @endforeach
            <th>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-success" type="button" wire:click="modePut()">
                        <i class="fa-regular fa-square-plus"></i>
                    </button>
                </div>
            </th>
        </tr>
        @foreach ($itemsTable as $_row)
            <tr>
                @foreach (collect($cols) as $_i)
                    @php
                        data_set($_i, 'output', \Nabre\Repositories\FormTwo\Field::STATIC);
                        data_set($_i, 'value', data_get($_row, data_get($_i, 'variable')));
                    @endphp
                    <td>
                        @include('Nabre::livewire.form-manage.item')
                    </td>
                @endforeach
                <td>
                    @include('Nabre::livewire.form-manage.table.body.manage')
                </td>
            </tr>
        @endforeach
    </table>
</div>
