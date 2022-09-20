<?php
namespace Nabre\Http\Requests;
//use Illuminate\Http\Request;

use Nabre\Http\FormRequest;
use Illuminate\Validation\Rule;
use Nabre\Forms\NavbarItemLabelForm;
use Nabre\Models\NavbarItemLabel;

class NavbarItemLabelRequest extends FormRequest{

    protected $form=NavbarItemLabelForm::class;

    function authorize(){
        return true;
    }

    function rulesAll(){
        if($this->method()=='PUT' && !$this->custom){
            return [];
        }
       return ["redirectTo"=>['required']];
    }
    function rulesStore(){
        return [
            'name'=>['required','unique:'.(new NavbarItemLabel)->getTableName().',name' ],
        ];
    }

    function rulesUpdate(){
        if($this->automatic || !$this->custom){
             return [];
        }
        return [
            'name'=>['required','unique:'.(new NavbarItemLabel)->getTableName().',name,'.$this->route('id').',_id' ],
        ];
    }
}
