<?php
namespace Nabre\Http\Requests;
//use Illuminate\Http\Request;

use Nabre\Forms\CollectionFieldForm;
use Nabre\Http\FormRequest;


class CollectionFieldRequest extends FormRequest{

    protected $form=CollectionFieldForm::class;

    function authorize(){
        return true;
    }

    function rulesAll(){
        return [];
    }

}
