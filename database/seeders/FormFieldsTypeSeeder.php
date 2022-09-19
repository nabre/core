<?php

namespace Nabre\Database\Seeders;

use Illuminate\Database\Seeder;
use Nabre\Models\FormFieldType as Model;
use Nabre\Repositories\Form\Field;

class FormFieldsTypeSeeder extends Seeder
{
    function run()
    {
        collect(Field::getConstants())->each(function ($key) {
            $name = null;
            switch ($key) {
                case Field::ADDRESS:
                    $name = ['it' => 'Indirizzo'];
                    break;
                case Field::BOOLEAN:
                    $name = ['it' => 'Boleano'];
                    break;
                case Field::HIDDEN:
                    $name = ['it' => 'Nascosto'];
                    break;
                case Field::LANG_SELECT:
                    $name = ['it' => 'Seleziona lingua'];
                    break;
                case Field::PASSWORD2:
                    $name = ['it' => 'Password2; non nasconde il valore durante la lettura'];
                    break;
                case Field::SELECT:
                    $name = ['it' => 'Menu a tendina'];
                    break;

                case Field::SELECT_MULTI:
                    $name = ['it' => 'Menu a tendina; selezione multipla'];
                    break;
                case Field::TEXT:
                    $name = ['it' => 'Stringa di testo'];
                    break;
                case Field::TEXT_LANG:
                    $name = ['it' => 'Stringa di testo, multilingua'];
                    break;
                case Field::TEXTAREA:
                    $name = ['it' => 'Area di testo'];
                    break;
            }
            $data = compact('name', 'key');
            Model::firstOrCreate(compact('key'), $data);
        });
    }
}
