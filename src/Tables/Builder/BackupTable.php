<?php

namespace Nabre\Tables\Builder;

use Collective\Html\HtmlFacade as Html;
use Nabre\Repositories\Table\Structure;

class BackupTable extends Structure
{
    function columns()
    {
        return ['datetime', 'buttons'];
    }

    function colButtons()
    {
        $btn =  Html::tag('button', '<i class="fa-solid fa-database"></i>', ['class' => 'btn btn-sm btn-info', 'title' => 'Restore', 'wire:click' => "restore('" . $this->item['basename'] . "')"])
            . Html::tag('button', '<i class="fa-solid fa-download"></i>', ['class' => 'btn btn-sm btn-warning', 'title' => 'Download', 'wire:click' => "download('" . $this->item['basename'] . "')"])
            . Html::tag('button', '<i class="fa-solid fa-trash-can"></i>', ['class' => 'btn btn-sm btn-danger', 'title' => 'Elimina', 'wire:click' => "destroy('" . $this->item['basename'] . "')"]);
        return Html::div($btn, ['class' => 'btn-group', 'role' => 'group']);
    }

    function query()
    {
        $disk = \Storage::disk('backup');
        $path = $disk->path('');
        return collect($disk->allFiles())->map(function ($file) use ($path) {
            $file = $path . $file;
            $info = pathinfo($file);
            list($info['db'], $info['date'], $info['time']) = explode('_', $info['filename']);
            $info['datetime'] = substr($info['date'], 0, 4) . "-" . substr($info['date'], 4, 2) . "-" . substr($info['date'], 6, 2) . " " . substr($info['time'], 0, 2) . ":" . substr($info['time'], 2, 2) . ":" . substr($info['time'], 4, 2);
            $info['modify']['time'] = filemtime($file);
            $info['modify']['date'] = date("Y-m-d H:i:s", filemtime($file));
            $info['size'] = filesize($file);
            return $info;
        })->sortBy('datetime', SORT_REGULAR, true);
    }
}
