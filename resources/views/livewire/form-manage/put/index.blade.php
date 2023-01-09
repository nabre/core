<form wire:submit.prevent="submit" class="container">
    <div class="row">
        @if (1)
            <div class="col-auto">
                <button class="btn btn-secondary" type="button" wire:click="modeTable">
                    <i class="fa-solid fa-angles-left"></i>
                </button>
                <hr>
            </div>
        @endif

        @include('Nabre::livewire.form-manage.title')
    </div>

    @include('Nabre::livewire.form-manage.put.print')

    @if (0)
        <hr>
        <div class="alert alert-danger">
            Il form non può essere inviato a causa di un errore di elaboraizone dei campi. Contattare
            l'amministratore.
        </div>
    @elseif(1)
        <hr>
        <button class="btn btn-info w-100" type="submit"><i class="fa-regular fa-floppy-disk"></i></button>
    @endif
</form>
