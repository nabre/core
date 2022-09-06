<?php
namespace Nabre\Http\Requests\User;

use Nabre\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\User;
use Nabre\Forms\User\UserForm;

class UserRequest extends FormRequest{

    protected $form=UserForm::class;

    function authorize(){
        return true;
    }

    function rulesUpdate(){
        $user= $this->route('id');
        $id=$user->_id??null;
        $rules= [
            'name'=>'required|min:5',
            'contact'=>'required',
            'password_change' => 'nullable|min:6',
            'password_change_confirmation' => 'required_with:password_change|same:password_change',
            'email'=>['required','email:dns','unique:'.(new User)->getTableName().',email,'.$id.',_id' ]
        ];

        $count= optional(optional($user)->contactList)->count()??null;
        if($count==1){
            unset($rules['contact']);
        }

        return $rules;
    }
}
