<?php
namespace Nabre\Http\Requests;
//use Illuminate\Http\Request;

use Nabre\Forms\NavbarGroupForm;
use Nabre\Http\FormRequest;
use Nabre\Models\NavbarGroup;

class NavbarGroupRequest extends FormRequest{

    protected $form=NavbarGroupForm::class;
    function authorize(){
        return true;
    }

    function rulesAll(){
      //  return ['navbarFormat'=>['required']];
    }

    function rulesStore(){
        return [
            'name'=>['required','unique:'.(new NavbarGroup)->getTableName().',name' ],
        ];
    }

    function rulesUpdate(){
        return [
            'name'=>['required','unique:'.(new NavbarGroup)->getTableName().',name,'.$this->route('id').',_id' ],
        ];
    }
}
