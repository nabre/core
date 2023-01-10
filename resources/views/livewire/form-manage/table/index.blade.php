<div class="row">
    @include('Nabre::livewire.form-manage.title')
</div>

<table class="table">
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
                <td>{{ \Nabre\Repositories\FormTwo\Field::generate($_i) }}</td>
            @endforeach
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-secondary" type="button"
                        wire:click="modePut('{{ data_get($_row, $modelKey) }}')">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn btn-danger" title="Elimina">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
</table>
