<?php
namespace Nabre\Http\Requests;
//use Illuminate\Http\Request;

use Nabre\Forms\NavbarFormatForm;
use Nabre\Http\FormRequest;
use Nabre\Models\NavbarFormat;

class NavbarFormatRequest extends FormRequest{

    protected $form=NavbarFormatForm::class;

    function authorize(){
        return true;
    }


    function rulesStore(){
        return [
            'name'=>['required','unique:'.(new NavbarFormat)->getTableName().',name' ],
            'slug'=>['required','unique:'.(new NavbarFormat)->getTableName().',slug' ],
        ];
    }

    function rulesUpdate(){
        return [
            'name'=>['required','unique:'.(new NavbarFormat)->getTableName().',name,'.$this->route('id').',_id' ],
            'slug'=>['required','unique:'.(new NavbarFormat)->getTableName().',slug,'.$this->route('id').',_id' ],
        ];
    }
}
