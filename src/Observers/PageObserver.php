<?php

namespace Nabre\Observers;

use Nabre\Events\Setting\PageEvent;
use Nabre\Models\Page as Model;

class PageObserver
{
    function saving(Model $model)
    {
        $model->lvl = !($count = count(array_filter(explode("/", $model->uri)))) ? 1 : $count;
    }

    function saved(Model $model)
    {
        event(new PageEvent($model));
    }
}
