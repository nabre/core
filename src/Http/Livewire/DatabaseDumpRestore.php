<?php

namespace Nabre\Http\Livewire;

use Livewire\Component;
use Nabre\Tables\Builder\BackupTable;
use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Nabre\Database\MongoDB\Backup\Execute;


class DatabaseDumpRestore extends Component
{
    function new()
    {
      //  Artisan::call('dump:mongodb');
    }

    function download($file)
    {
        return \Storage::disk('backup')->download($file);
    }

    function restore($file)
    {
        Execute::restore($file);
        return redirect()->route("nabre.builder.database.index");
    }

    function destroy($file){
        \Storage::disk('backup')->delete($file);
    }

    function render()
    {
        //$bkp = Html::tag('button', '<i class="fa-solid fa-floppy-disk"></i> Backup', ['class' => 'btn btn-sm btn-light','title'=>'Backup', 'href' => route('nabre.builder.mongo.dump.index')]);
        $bkp=Html::div('Per eseguire un nuovo punto di salvataggio di tutto il database.<hr>eseguire:<br>php artisan dump:mongodb',['class'=>'alert alert-dark']);
        return '<div>' . $bkp . (new BackupTable())->html() . '</div>';
    }
}
