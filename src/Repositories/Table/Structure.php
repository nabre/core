<?php

namespace Nabre\Repositories\Table;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Facades\Gate;
use Nabre\Services\CollectionService;

class Structure
{
    var $actionMode;
    var $actions;
    var $modifyCol = false;
    var $data;
    var $columns;
    var $model;
    var $col;
    var $item;

    function __construct()
    {
        $this->actionMode = ['create', 'position', 'show', 'edit', 'copy', 'reset', 'destroy'];
        $this->setIcon('create', 'fa-solid fa-plus', 'success');
        $this->setIcon('show', 'fa-regular fa-eye', 'light');
        $this->setIcon('edit', 'fa-regular fa-pen-to-square', 'info');
        $this->setIcon('copy');
        $this->setIcon('reset');
        $this->setIcon('destroy', 'fa-regular fa-trash-can', 'danger');
    }

    function table()
    {
    }

    function columns()
    {
    }

    function actions()
    {
    }

    function query()
    {
        return $this->model::all();
    }


    function setIcon($name, $icon = null, $theme = null)
    {

        collect(['icon', 'theme'])->each(function ($i) use ($name, $icon, $theme) {
            $this->iconAction[$name][$i] = $$i ?? $this->iconAction[$name][$i] ?? null;
        });
        return $this;
    }

    function html()
    {
        $this->data = $this->data ?? $this->query();
        $this->table();

        $tableContent = [];
        $columns = $this->columns??collect($this->columns());

        $this->actions = collect($this->actions())->filter(function ($v, $k) {
            return in_array($k, $this->actionMode) && \Route::Has($v);
        })->sortBy(function ($v, $k) {
            return array_search($k, $this->actionMode);
        });

        ##row head
        $theadColsName = $columns->map(function ($colName) {
            $colName = $this->colName[$colName] ?? (is_null($this->model) ? null : CollectionService::getString(new $this->model, $colName)) ?? $colName;
            return Html::th($colName);
        });

        #btn create
        if(!is_null($this->model)){
            $createRoute = $this->actions->filter(function ($route, $name) {
                return $name == 'create' && Gate::allows($name, new $this->model);
            })->first();
        }


        if (!is_null($createRoute)) {
            $icon = $this->iconAction['create']['icon'];
            $theme = $this->iconAction['create']['theme'];
            $content = Html::th(Html::a('<i class="' . $icon . '"></i>', ['class' => 'btn btn-' . $theme . ' btn-sm', 'href' => route($createRoute)]));
            $theadColsName->push($content);
        }
        ##

        ##tbody

        $positionRoute = $this->actions->filter(function ($route, $name) {
            return $name == 'position' && Gate::allows('edit', new $this->model);
        })->first();

        $modifyItem = $this->actions->reject(function ($v, $k) {
            return in_array($k, ['create', 'position']);
        });

        $tbody = $this->data->map(function ($i) use ($columns, $modifyItem) {
            $this->item = $i;
            $row = $columns->map(function ($col) {
                return $this->defineCol($col);
            });

            if ($modifyItem->count()) {
                $modifyItem = $modifyItem->filter(function ($route, $name) use ($i) {
                    switch ($name) {
                        case "destroy":
                            $nameGate = 'delete';
                            break;
                        case "copy":
                        case "reset":
                        case "edit":
                            $nameGate = 'update';
                            break;
                        case "show":
                            $nameGate = 'view';
                            break;
                        default:
                            $nameGate = $name;
                            break;
                    }
                    return Gate::allows($nameGate, $i);
                })->map(function ($route, $name) use ($i) {
                    $href = route($route, $i->id);
                    $icon = $this->iconAction[$name]['icon'] ?? null;
                    $theme = $this->iconAction[$name]['theme'] ?? null;
                    switch ($name) {
                        case "show":
                            $class = 'btn btn-sm btn-' . $theme;
                            $btn = Html::a('<i class="' . $icon . '"></i>', compact('href', 'class'));
                            break;
                        case "edit":
                            $class = 'btn btn-sm btn-' . $theme;
                            $btn = Html::a('<i class="' . $icon . '"></i>', compact('href', 'class'));
                            break;
                        case "destroy":
                            $modalId = "destroyConfirm-" . $i->id;
                            $string = "Conferma";

                            $header = Html::tag('h5', $string, ['class' => 'modal-title']) .
                                Html::btn(null, ['class' => 'btn-close', 'data-bs-dismiss' => "modal", 'aria-label' => "Close"]);
                            $footer = Html::btn('Elimina', ['class' => 'btn btn-' . $theme, 'type' => 'submit']);
                            $footer .= Form::hidden('_token', csrf_token());
                            $footer .= Form::hidden('_method', 'delete');

                            $content = Html::div(
                                Html::div($header, ['class' => 'modal-header']) .
                                    Html::tag('form', $footer, ['class' => 'modal-footer', 'method' => 'post', 'action' => $href]),
                                ['class' => 'modal-content']
                            );
                            $dialog = Html::div($content, ['class' => 'modal-dialog']);
                            $modal = Html::div($dialog, ['class' => 'modal fade', 'id' => $modalId, 'tabindex' => -1, 'aria-hidden' => 'true']);
                            $class = 'btn btn-sm btn-' . $theme;
                            ${"data-bs-toggle"} = "modal";
                            ${"data-bs-target"} = "#" . $modalId;
                            $btn = Html::btn('<i class="' . $icon . '"></i>', compact('class', "data-bs-toggle", "data-bs-target")) . $modal;
                            break;
                    }
                    return $btn;
                });

                if ($modifyItem->count()) {
                    $this->modifyCol = true;
                }

                $div = Html::div(implode('', (array)$modifyItem->toArray()), ['class' => 'btn-group']);
                $row->push(Html::td($div));
            }
            return Html::tr(implode('', (array)$row->toArray()));
        });
        $tableContent[] = Html::tbody(implode('', (array)$tbody->toArray()));
        ##

        if ($this->modifyCol && is_null($createRoute)) {
            $theadColsName = $theadColsName->push(Html::th(""));
        }

        $theadCols[] = implode('', $theadColsName->toArray());
        $thead = Html::tr(implode('', (array)$theadCols));
        $tableContent[] = Html::thead($thead);

        //$tfootCols = '';$tfoot = Html::tr($tfootCols);
        //$caption = 'didascalia';Html::caption($caption)
        //Html::tfoot($tfoot);

        $tableContent = implode('', (array)$tableContent);
        $table = Html::table($tableContent, ['class' => 'table table-sm table-bordered w-auto']);
        return Html::div($table, ['class' => 'table-responsive']);
    }

    protected function defineCol($col)
    {
        $cont = $this->item;
        $cast = null;
        collect(explode(".", $col))->each(function ($part) use (&$cont, &$cast) {
            $cast = optional($cont)->getCasts()[$part] ?? null;
            if (is_array($cont)) {
                $cont = optional($cont)[$part];
            } else {
                $cont = optional($cont)->$part;
            }
        });
        $this->col = $cont;

        if ($fn = $this->customCol($col)) {
            $cont = $this->$fn();
        } else {
            $cont = Columns::cast($cast, $this->col);
        }

        return Html::td($cont);
    }

    protected function customCol($col)
    {
        $fn = 'col' . ucfirst($col);
        if (method_exists($this, $fn)) {
            return $fn;
        }
        return false;
    }
}
