<?php

namespace Nabre\Providers;

//use Livewire\LivewireServiceProvider as ServiceProvider;
use Illuminate\Support\ServiceProvider;
use Nabre\Http\Livewire\DatabaseDumpRestore;
use Nabre\Http\Livewire\TableRender;
use Livewire;
use Nabre\Http\Livewire\FormEmbed;
use Nabre\Http\Livewire\FormManage;
use Nabre\Http\Livewire\NavigationConsole;

class LivewireServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //  parent::boot();
        Livewire::component('formembed',FormEmbed::class);
        Livewire::component('form',FormManage::class);
        Livewire::component('databasedumprestore', DatabaseDumpRestore::class);
        Livewire::component('tablerender', TableRender::class);
        Livewire::component('navigationconsole', NavigationConsole::class);
    }
}
