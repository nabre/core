<?php

namespace Nabre\Repositories\FormTwo;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Str;
use Nabre\Models\FormFieldType;
use Nabre\Repositories\LocalizationRepositorie;
use Nabre\Repositories\Livewire;
use ReflectionClass;

class Field
{
    // Simple fields
    const TEXT = 'text';
    const TEXTAREA = 'textarea';
    const TEXTAREA_CKEDITOR = 'ckeditor';
    const SELECT = 'select';
    const SELECT_MULTI = 'select-multiple';
    //  const CHOICE = 'choice'; //
    const CHECKBOX = 'checkbox';
    const BOOLEAN = 'bool';
    const RADIO = 'radio';
    const PASSWORD = 'password';
    const PASSWORD2 = 'password2';
    const HIDDEN = 'hidden';
    //  const FILE = 'file'; //
    const STATIC = 'static';
    //Date time fields
    //   const DATE = 'date';
    //   const DATETIME = 'datetime-local';
    //   const DATETIME_LOCAL = 'datetime-local';
    //    const MONTH = 'month';
    //    const TIME = 'time';
    //    const WEEK = 'week';
    //Special Purpose fields
    const ADDRESS = 'address';
    const LIVEWIRE = 'livewire';
    //const COLOR = 'color';
    //const SEARCH = 'search'; //
    // const IMAGE = 'image'; //
    const EMAIL = 'email';
    // const URL = 'url'; //
    // const TEL = 'tel'; //
    //  const NUMBER = 'number'; //
    //  const RANGE = 'range'; //
    const TEXT_LANG = 'text-lang';
    //   const ENTITY = 'entity'; //
    //  const FORM = 'form'; //
    //Embeds
    const EMBEDS_MANY = 'embeds-many';
    const EMBEDS_ONE = 'embeds-one';
    //Other
    const MSG = 'message';
    const HTML = 'html';
    const FIELD_TYPE_LIST = 'field-type-list';
    const LANG_SELECT = 'lang-select';
    //Buttons
    //const BUTTON_SUBMIT = 'submit';
    //const BUTTON_RESET = 'reset'; //
    //const BUTTON_BUTTON = 'button'; //


    static function fieldsListRequired()
    {
        return [self::SELECT, self::SELECT_MULTI, self::CHECKBOX, self::RADIO];
    }
    static function classAdd(&$class, $edit)
    {
        $class = explode(' ', implode(' ', (array)($class ?? null)));
        $edit = explode(' ', implode(' ', (array)($edit ?? null)));
        $class = array_values(array_unique(array_merge($class, $edit)));
    }
    static function name(&$name)
    {
        $name = collect(explode(".", $name))->map(function ($str, $k) {
            if ($k) {
                $str = '[' . $str . ']';
            }
            return $str;
        })->implode('');
    }

    static function generate($it)
    {

        if (optional(data_get($it, 'errors'))->count()) {
            $msg = data_get($it, 'errors')->implode('<br>');
            return '<div class="alert alert-danger m-0 p-1">' . $msg . '</div>';
        }

        $html = '';

        $value = data_get($it, 'value');
        $output = data_get($it, 'output');
        $list = collect(data_get($it, 'set.list.items', []));
        $empty = data_get($it, 'set.list.empty');
        $disabled = data_get($it, 'set.list.disabled', []);
        $options = data_get($it, 'set.options', []);
        $options['wire:model.defer']=$options['wire:model'];
        unset($options['wire:model']);
        $name = $options['wire:model.defer'] ?? null;
        $errors = data_get($it, 'errors_print', null);

        if (!is_null($errors)) {
            if ($errors) {
                self::classAdd($options['class'], 'is-invalid');
            } else {
                self::classAdd($options['class'], 'is-valid');
            }
        }

        switch ($output) {
                ###
            case self::PASSWORD:
                $value = null;
            case self::PASSWORD2:
                self::classAdd($options['class'], "form-control");
                //     self::name($name);
                $html .= Form::passwordToggle(null, $value, $options);
                break;
                ###
            case self::TEXT:
            case self::EMAIL:
                self::classAdd($options['class'], "form-control");
                //  self::name($name);
                $html .= Form::input($output, null, null, $options);
                break;
                ###
            case self::TEXTAREA_CKEDITOR:
                self::classAdd($options['class'], 'ckeditor');
            case self::TEXTAREA:
                self::classAdd($options['class'], "form-control");
                //     self::name($name);
                $html = Form::textarea(null, null, $options);
                break;
                ###

            case self::SELECT_MULTI:
                $options[] = 'multiple';
                //   $name .= '[]';
            case self::SELECT:
                self::classAdd($options['class'], "form-select");

                $html = Form::select('', $list, null, $options);
                break;
                ###
            case self::FIELD_TYPE_LIST:
                data_set($it, 'output', self::SELECT);
              //  data_set($it, 'set.list.empty', '-Seleziona-');
                $list = FormFieldType::get()->pluck('string', 'key')->sort();
                $list = $list->reject(function ($v, $k) {
                    return in_array($k, [self::LIVEWIRE, self::MSG, self::EMBEDS_MANY, self::EMBEDS_ONE]);
                })->sort();
                data_set($it, 'set.list.items', $list);
                $html = self::generate($it);
                break;
            case self::LANG_SELECT:
                $list = (new LocalizationRepositorie)->aviableLang()->pluck('language', 'lang')->sort();
                data_set($it, 'output', self::SELECT);
                data_set($it, 'set.list.items', $list);
                $html = self::generate($it);
                break;
            case self::BOOLEAN:
                $options['role'] = 'switch';
                self::classAdd($options['class'], "form-check-input");
                /*   if ($value) {
                    $options[] = 'checked';
                }*/
                $html = Html::div(
                    Form::hidden(null, 0, ['id' => null]) .
                        Form::input('checkbox', null, true, $options),
                    ['class' => 'form-check form-switch']
                );
                break;
                ###
            case self::RADIO:
            case self::CHECKBOX:
                //  data_set($options,'type',$output);
                self::classAdd($options['class'], "form-check-input");
                $nCk = Str::random(10);

                $html = $list->map(function ($v, $k) use ($it,  $options, $disabled, $output, $nCk) {
                    data_set($options, 'id', Str::random(10));

                    if (in_array($k, $disabled)) {
                        $options[] = 'disabled';
                    }
                    return '<div class="form-check">' . Form::$output(self::RADIO == $output ? $nCk : '', $k, null,  $options) . " " . Form::label(data_get($options, 'id'), $v, ['class' => "form-check-label"]) . '</div>';
                })->implode('');
                break;
            case self::TEXT_LANG:
                $langs = (new LocalizationRepositorie)->aviableLang();
                $numLangs = $langs->count();

                //     self::name($name);
                $langs->sortBy(['position', 'language'])->values()->each(function ($i) use (&$html, $options, $numLangs) {
                    //   $name .= '[' . data_get($i, 'lang') . ']';
                    if (!is_null(data_get($options, 'wire:model'))) {
                        $options['wire:model'] .= "." . data_get($i, 'lang');
                    }
                    //   $value = data_get($value, data_get($i, 'lang'));
                    self::classAdd($options['class'], "form-control");
                    $input = Form::input('text', null, null, $options);
                    if ($numLangs == 1) {
                        $html .= $input;
                    } else {
                        $span = Html::tag('span', data_get($i, 'icon'), ['class' => 'input-group-text', "title" => data_get($i, 'language')]);
                        $html .= Html::div($span . $input, ['class' => 'input-group mb-1']);
                    }
                });
                break;
                ###
            case self::MSG:
                $html = Html::div(data_get($value, 'text'), ['class' => 'alert p-1 alert-' . data_get($value, 'theme')]);
                break;
                ###
            case self::HTML:
                $html = data_get($value, 'html');
                break;
            case self::STATIC:
                /*  if (!is_null($list ?? null) && optional($list)->count()) {
                    $value = collect((array)$value)->map(function ($v) use ($list) {
                        return data_get($list, $v) ?? null;
                    })->unique()->values()->toArray();
                }*/

                $html = self::valuePrint($value);
                break;
                ###
            case self::EMBEDS_ONE:
            case self::EMBEDS_MANY:
                //   $embed = data_get($it, 'embed');
                //   $html = livewire('formembed', compact('embed'));
                break;
                ###
            default:
                $html = 'non-definito<br>';
                $output = self::HIDDEN;
            case self::HIDDEN:
                $html .= Form::input($output, null, null, $options);
                //  $value = (array)$value;
                //     self::name($name);
                /*    switch (count($value)) {
                    default:
                        $name .= '[]';
                    case 1:
                       collect($value)->each(function ($value) use (&$html, $name, $options, $output) {
                            $html .= Form::input($output, $name, $value, $options);
                        });
                    case 0:
                        if(data_get($options, 'wire:model', false)){
                            $html .= Form::input($output, $name, $value, $options);
                        }
                        break;
                }*/
                break;
        }

        if (!is_null($errors)) {
            $id = data_get($options, 'id', data_get($it, 'variable'));
            $id .= "Feedback";
            if ($errors) {
                $mode = 'invalid';
                $msg = collect($errors)->implode('<br>');
                $html .= '<div id="' . $id . '" class="' . $mode . '-feedback">' . $msg . '</div>';
            }
        }


        return $html;
    }

    static private function valuePrint($value)
    {

        if (is_array($value)) {
            $content = collect($value)->map(function ($value) {
                return (string)Html::div(self::valuePrint($value), ['class' => 'list-group-item p-1']);
            })->implode('');
            $value = empty($content) ? null : Html::div($content, ['class' => 'list-group']);
        }

        return $value;
    }

    static function getConstants()
    {
        $oClass = new ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}
