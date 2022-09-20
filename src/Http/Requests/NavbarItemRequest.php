<?php
namespace Nabre\Http\Requests;

use Nabre\Http\FormRequest;
use Nabre\Forms\NavbarItemForm;

class NavbarItemRequest extends FormRequest{

    protected $form=NavbarItemForm::class;

    function authorize(){
        return true;
    }

    function rulesAll(){
        return [
            'navbarGroup'=>['required'],
            'navbarItemLabel'=>['required'],
            'navbarItemFormat'=>'required',
        ];
    }
}
