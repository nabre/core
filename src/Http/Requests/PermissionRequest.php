<?php
namespace Nabre\Http\Requests;
//use Illuminate\Http\Request;

use Nabre\Http\FormRequest;
use Illuminate\Validation\Rule;
use Nabre\Forms\PermissionForm;

class PermissionRequest extends FormRequest{

    protected $form=PermissionForm::class;

    function authorize(){
        return true;
    }

    function rulesAll(){
        return ['name'=>'required'];
    }
}
