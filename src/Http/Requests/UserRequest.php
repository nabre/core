<?php
namespace Nabre\Http\Requests;

use Nabre\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\User;
use Nabre\Forms\UserForm;

class UserRequest extends FormRequest{

    protected $form=UserForm::class;

    function authorize(){
        return true;
    }

    function rulesAll(){
        return [
            'name'=>'required|min:5',
        ];
    }

    function rulesStore(){
        return ['email'=>['required','email:dns','unique:'.(new User)->getTableName().',email' ]];
    }

    function rulesUpdate(){
        return ['email'=>['required','email:dns','unique:'.(new User)->getTableName().',email,'.$this->route('id').',_id' ]];
    }
}
