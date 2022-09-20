<?php
namespace Nabre\Http\Requests;
//use Illuminate\Http\Request;

use Nabre\Forms\CollectionForm;
use Nabre\Http\FormRequest;


class CollectionRequest extends FormRequest{

    protected $form=CollectionForm::class;

    function authorize(){
        return true;
    }

    function rulesAll(){
        return ['model'=>'required'];
    }

}
