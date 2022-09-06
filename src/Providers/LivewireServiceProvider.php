<?php namespace Nabre\Providers;

//use Livewire\LivewireServiceProvider as ServiceProvider;
use Illuminate\Support\ServiceProvider;
use Nabre\Http\Livewire\DatabaseDumpRestore;
use Nabre\Http\Livewire\FormEmbedsMany;
use Nabre\Http\Livewire\TableRender;

class LivewireServiceProvider extends ServiceProvider
{
    public function boot()
    {
      //  parent::boot();
        \Livewire::component('formembedsmany', FormEmbedsMany::class);
        \Livewire::component('databasedumprestore', DatabaseDumpRestore::class);
        \Livewire::component('tablerender', TableRender::class);
    }
}
