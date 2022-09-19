<?php

namespace Nabre\Database\Seeders;

use Illuminate\Database\Seeder;
use Nabre\Models\Role as Model;

class RoleSeeder extends Seeder
{
    function run()
    {
        collect([
            ['guard_name'=>'web',"name"=>"builder","priority"=>1,"slug"=>['it'=>"Costruttore"]],
            ['guard_name'=>'web',"name"=>"admin","priority"=>2,"slug"=>['it'=>"Amministratore"]],
            ['guard_name'=>'web',"name"=>"manage","priority"=>3,"slug"=>['it'=>"Gestione"]],
        ])->each(function($data){
            $name=data_get($data,'name');
            Model::firstOrCreate(compact('name'),$data);
        });
    }
}
