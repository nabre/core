<?php

namespace Nabre\Observers;

use Nabre\Models\Collection as Model;
use Nabre\Services\CollectionService;

class CollectionObserver
{
    function created(Model $model){
        CollectionService::syncField($model);
    }
}
