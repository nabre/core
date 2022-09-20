<?php
namespace Nabre\Http\Requests;
//use Illuminate\Http\Request;

use Nabre\Forms\NavbarItemFormatForm;
use Nabre\Http\FormRequest;
use Nabre\Models\NavbarItemFormat;

class NavbarItemFormatRequest extends FormRequest{

    protected $form=NavbarItemFormatForm::class;
    function authorize(){
        return true;
    }


    function rulesStore(){
        return [
           // 'name'=>['required','unique:'.(new NavbarItemFormat)->getTableName().',name' ],
            'slug'=>['required','unique:'.(new NavbarItemFormat)->getTableName().',slug' ],
        ];
    }

    function rulesUpdate(){
        return [
          //  'name'=>['required','unique:'.(new NavbarItemFormat)->getTableName().',name,'.$this->route('id').',_id' ],
            'slug'=>['required','unique:'.(new NavbarItemFormat)->getTableName().',slug,'.$this->route('id').',_id' ],
        ];
    }
}
