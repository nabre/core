<?php
namespace Nabre\Http\Requests\User;

use Nabre\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\User;
use Nabre\Forms\User\ContactForm;

class ContactRequest extends FormRequest{

    protected $form=ContactForm::class;
    function authorize(){
        return true;
    }

    function rulesUpdate(){
        $table=$id=null;
        $contact=$this->route('id');
        $user=optional(optional($contact)->user)??null;
        $table=optional($user)->getTableName()??null;
        $id=optional($user)->_id;
        //dd($table,$id);
        return ['firstname'=>'required',
            'lastname'=>'required',
            'email'=>['nullable','email:dns','unique:'.$table.',email,'.$id.',_id']

        /*   'name'=>'required|min:5',
            'password_change' => 'nullable|min:6',
            'password_change_confirmation' => 'required_with:password_change|same:password_change',
        'email'=>['required','email:dns','unique:'.(new User)->getTableName().',email,'.$this->route('id').',_id' ]*/
        ];
    }
}
