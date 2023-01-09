<div class="row">
    @include('Nabre::livewire.form-manage.title')
</div>

<table class="table">
    <th>
    <td>
        <button class="btn btn-success" type="button" wire:click="modePut()">
            <i class="fa-regular fa-square-plus"></i>
            </button>
    </td>
    </th>
    @foreach ($itemsTable as $_row)
        <tr>
            <td>
                <button class="btn btn-secondary" type="button" wire:click="modePut('{{ data_get($_row, $modelKey) }}')">
                    <i class="fa-regular fa-pen-to-square"></i>
                </button>
            </td>
        </tr>
    @endforeach
</table>
