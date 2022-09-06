<?php

namespace Nabre\Repositories\Form;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Nabre\Models\FormFieldType;
use Nabre\Repositories\LocalizationRepositorie;
use Nabre\Repositories\Livewire;
use ReflectionClass;

class Field
{
    // Simple fields
    const TEXT = 'text';
    const TEXTAREA = 'textarea';
    const SELECT = 'select';
    const SELECT_MULTI = 'select-multiple';
    //  const CHOICE = 'choice'; //
    const CHECKBOX = 'checkbox';
    const BOOLEAN = 'bool';
    //   const RADIO = 'radio';
    const PASSWORD = 'password';
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
    const FIELD_TYPE_LIST = 'field-type-list';
    const LANG_SELECT = 'lang-select';
    //Buttons
    //const BUTTON_SUBMIT = 'submit';
    //const BUTTON_RESET = 'reset'; //
    //const BUTTON_BUTTON = 'button'; //

    static function classAdd(&$class, $edit)
    {
        $class = explode(' ', implode('', (array)($class ?? null)));
        $edit = explode(' ', implode('', (array)($edit ?? null)));
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
        $html = '';
        $name = $it['variable'];
        $value = old($name, $it['value'] ?? null);
        $output = $it['output'];
        $list = $it['set']['list']['items'] ?? collect([]);
        $empty = $it['set']['list']['empty'] ?? null;
        $disabled = (array)($it['set']['list']['disabled'] ?? null);
        $options = (array)($it['set']['options'] ?? null);
        $errors = session('errors');

        if (!is_null($errors)) {
            if ($errors->has($name)) {
                self::classAdd($options['class'], 'is-invalid');
            }
        }

        switch ($output) {
            case self::PASSWORD:
                $value = null;
            case self::TEXT:
            case self::EMAIL:
                self::classAdd($options['class'], "form-control");
                self::name($name);
                $html .= Form::input($output, $name, $value, $options);
                break;
                ###
            case self::TEXTAREA:
                self::classAdd($options['class'], "form-control");
                self::name($name);
                $html = Form::textarea($name, $value, $options);
                break;
                ###
            case self::SELECT_MULTI:
                $options[] = 'multiple';
                $name .= '[]';
            case self::SELECT:
                self::classAdd($options['class'], "form-select");

                if (!is_null($empty)) {
                    $list = $list->prepend($empty);
                }

                self::name($name);
                $html = Form::select($name, $list, $value, $options);
                break;
                ###
            case self::FIELD_TYPE_LIST:
                $it['output'] = self::SELECT;
                $it['set']['list']['empty'] = '-Seleziona-';

                $list = FormFieldType::get()->pluck('string', 'key')->sort();

                /*
                $list = self::getConstants();
                $list = collect(array_combine($list, $list));
                */

                $list = $list->reject(function ($v, $k) {
                    return in_array($k, [self::LIVEWIRE, self::MSG, self::EMBEDS_MANY, self::EMBEDS_ONE]);
                })->sort();
                $it['set']['list']['items'] = $list;
                $html = self::generate($it);
                break;
            case self::LANG_SELECT:
                $list = (new LocalizationRepositorie)->aviableLang()->pluck('language', 'lang')->sort();
                $it['output'] = self::SELECT;
                $it['set']['list']['items'] = $list;

                $html = self::generate($it);

                break;
            case self::BOOLEAN:
                $options['role'] = 'switch';
                self::classAdd($options['class'], "form-check-input");
                if ($value) {
                    $options[] = 'checked';
                }
                $html = Html::div(
                    Form::hidden($name, 0) .
                        Form::input('checkbox', $name, true, $options),
                    ['class' => 'form-check form-switch']
                );
                break;
                ###
            case self::CHECKBOX:
                self::classAdd($options['class'], "form-check-input");
                $value = (array)$value;
                self::name($name);

                $html = Form::hidden($name, null);
                $name .= '[]';
                $html .= $list->filter(function ($v, $k) use ($it, $value, $disabled) {
                    return in_array($k, $disabled) && in_array($k, $value);
                })->map(function ($v, $k) use ($name) {
                    $html = Form::hidden($name, $k);
                    return compact('html');
                })->implode('html');

                $html .= collect($list)->map(function ($v, $k) use ($value, $it, $name, $options, $disabled) {
                    $options['id'] = $it['variable'] . '-' . $k;

                    if (in_array($k, $disabled)) {
                        $options[] = 'disabled';
                    }
                    $bool = in_array($k, $value);
                    $html = '<div class="form-check">' . Form::checkbox($name, $k, $bool, $options) . " " . Form::label($options['id'], $v, ['class' => "form-check-label"]) . '</div>';
                    return compact('html');
                })->implode('html');
                break;
                ###
            case self::EMBEDS_ONE:
                /* $html .= '<ul class="list-group">';
                $eForm = new $it['set']['embeds']['model'];
                $eForm->prefix = $it['variable'];
                $eForm = $eForm->collection($it['set']['rel']->model);
                $eForm = $eForm->data($value);
                $eForm = $eForm->generate();
                $eForm = $eForm->add('_id', Field::HIDDEN)->lastInsert();
                $build = (new Build)->structure($eForm);
                $embed = $build->embedHtml($value);
                $html .= '<li class="list-group-item">' . $embed . '</li>';
                $html .= '</ul>';*/
                $html = 'definisci codice';
                break;
                ###
            case self::EMBEDS_MANY:
                $prefix = $it['variable'];
                $embedsModel = $it['set']['rel']->model;
                $toPut = $it['set']['embeds'];
                $html = Form::hidden($prefix) . Livewire::load('formembedsmany', compact('value', 'prefix', 'embedsModel', 'toPut'));
                break;
                ###
            case self::TEXT_LANG:
                $langs = (new LocalizationRepositorie)->aviableLang();
                $numLangs = $langs->count();

                self::name($name);
                $langs->sortBy(['position', 'language'])->values()->each(function ($i) use ($name, &$html, $value, $options, $numLangs) {
                    $name .= '[' . $i->lang . ']';
                    if (!is_null($options['wire:model.defer'] ?? null)) {
                        $options['wire:model.defer'] .= "." . $i->lang;
                    }
                    $value = $value[$i->lang] ?? null;
                    self::classAdd($options['class'], "form-control");
                    $input = Form::input('text', $name, $value, $options);
                    if ($numLangs == 1) {
                        $html .= $input;
                    } else {
                        $span = Html::tag('span', $i->icon, ['class' => 'input-group-text', "title" => $i->language]);
                        $html .= Html::div($span . $input, ['class' => 'input-group mb-1']);
                    }
                });
                break;
                ###
            case self::MSG:
                $html = Html::div($value['text'], ['class' => 'alert p-1 alert-' . $value['theme']]);
                break;
                ###
            case self::STATIC:
                $html="static variable";

              /*  if (!is_null($list ?? null)) {
                    $value = collect((array)$value)->map(function ($v) use ($it) {
                        return $list[$v] ?? null;
                    })->unique()->values()->toArray();
                }

                if (is_array($value)) {
                    $value = implode(", ", $value);
                }

                $html = $value;*/
                break;
                ###
            default:
                $output = self::HIDDEN;
            case self::HIDDEN:
                $value = (array)$value;
                self::name($name);
                switch (count($value)) {
                    default:
                        $name .= '[]';
                    case 1:
                        collect($value)->each(function ($value) use (&$html, $name, $options, $output) {
                            $html .= Form::input($output, $name, $value, $options);
                        });
                    case 0:
                        break;
                }
                break;
        }

        if (!is_null($errors)) {
            $id = $options['id'] ?? $it['variable'];
            $id .= "Feedback";
            if ($errors->has($name)) {
                $mode = 'invalid';
                $msg = $errors->first($name);
                $html .= '<div id="' . $id . '" class="' . $mode . '-feedback">' . $msg . '</div>';
            }
        }


        return $html;
    }

    static function getConstants()
    {
        $oClass = new ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}
