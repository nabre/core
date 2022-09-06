<?php

namespace Nabre\Repositories;
use Config;
use App;
use Session;

class LocalizationRepositorie{

    function aviableLang(){
        return collect(Config::get('app.available_locales'))->map(function($lang,$language){
            switch($lang){
                case "en":
                $flagLang='gb';
                break;
                default:
                $flagLang=$lang;
                break;
            }
            $position=!( (Config::get('app.fallback_locale')??'en') ==$lang)+1;
            $icon='<span class="fi fi-'.$flagLang.'"></span>';
            return (object) get_defined_vars() ;
        })->sortBy('language')->values();
    }

    function boolLangMenu(){
        return count((array) Config::get('app.available_locales') )>1;
    }

    function menuSettings(&$aviableLocale,&$currentLocale){
        $aviableLocale=$this->aviableLang()->sortBy('language')->values();
        $currentLocale=$this->currentLocale($aviableLocale);
        return $this;
    }

    function currentLocale($aviableLocale=null){
        $aviableLocale=$aviableLocale??$this->aviableLang();
        return $aviableLocale->filter(function($q){
            return $q->lang==App::currentLocale();
        })->first();
    }

    function localeMiddleware(){
        if (Session::has('locale')) {
            $locale=Session::get('locale');
        }else{
            $locale=$this->localeAutoFind();
        }
        App::setLocale($locale);
        return $this;
    }

    function localeAutoFind(){

        $langArr = $this->aviableLang()->pluck('lang')->unique()->values()->toArray();
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $languages = collect(explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']))->map(function($str){
                return substr($str,0,2);
            })->unique()->values()->toArray();
        }
        return (!is_null(auth()->user())?Config::get('app.locale'):null)??collect(array_values(array_intersect((array) $languages,$langArr)))->first()??Config::get('app.locale')??Config::get('app.fallback_locale')??'en';
    }

    function formEdit($array,$nameVariable){
        $array=(array)$array;
        $array=collect($array);
        return $this->aviableLangRequest()->map(function($l)use($array,$nameVariable){
            $l->value=$array[$l->lang]??null;
            $l->nameVariable=$nameVariable."[".$l->lang."]";
            return $l;
        });
    }

    function aviableLangRequest(){
        return $this->aviableLang()->sortBy(['position','language'])->values();
    }

    function string($array){
        if(!is_array($array)){
         //  $array=['it'=>(string)$array];
        }

        $array=(array)$array;
        $sorting=array_values(array_unique([App::currentLocale(),Config::get('app.locale'),Config::get('app.fallback_locale')]));
        $key=array_values(array_intersect($sorting, array_keys(array_filter((array)$array, 'strlen')) ))[0]??null;
        return $array[$key]??null;
    }
}
