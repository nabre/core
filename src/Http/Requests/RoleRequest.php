<?php
namespace Nabre\Http\Requests;
//use Illuminate\Http\Request;

use Nabre\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\User;
use Nabre\Forms\RoleForm;

class RoleRequest extends FormRequest{

    protected $form=RoleForm::class;

    function authorize(){
        return true;
    }

    function rulesStore(){
        return ['name'=>'required'];
    }

}
