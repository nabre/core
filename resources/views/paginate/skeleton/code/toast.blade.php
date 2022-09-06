<?php $errors=$errors??null;?>
@if (!is_null($errors))
    <div class="toast-container p-3 position-absolute bottom-0 end-0" >
        @if ($errors->any())
            <div class="toast bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                <strong class="me-auto">Error!</strong>
                <small>compilazione</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
                </div>
            </div>
        @endif
    </div>
@endif
