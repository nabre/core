<div class="btn-group btn-group-sm">
    <button class="btn btn-primary" type="button" wire:click="modePut('{{ data_get($_row, $modelKey) }}')">
        <i class="fa-regular fa-pen-to-square"></i>
    </button>
    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
        data-bs-target="#confirmDestroy-{{ data_get($_row, $modelKey) }}">
        <i class="fa-solid fa-trash-can"></i>
    </button>
</div>


<div class="modal fade" id="confirmDestroy-{{ data_get($_row, $modelKey) }}" tabindex="-1"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Conferma eliminazione</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('Nabre::livewire.form-manage.table.body.manage-preview-destroy')
            </div>
            <div class="modal-footer">
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close</button>
                    <button type="button" class="btn btn-danger"  data-bs-dismiss="modal"
                        wire:click="destroy('{{ data_get($_row, $modelKey) }}')">
                        Elimina
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
