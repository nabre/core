<?php

namespace Nabre\Tables\Manage;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Nabre\Repositories\Table\Structure;
use App\Models\UserContact as Model;
use Nabre\Services\Html\UserStatus;

class ContactTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return ['lastname', 'firstname', 'email', 'phone', 'permissions', 'user'];
    }

    function colUser()
    {
        $html = '';
        switch ($this->col) {
            case null:
                if (auth()->user()->can('userCreate', $this->item)) {
                    $class = 'd-inline w-auto';
                    $method = "POST";
                    $url = route('nabre.manage.contat.userGenerate', $this->item->id);
                    $generate = Form::open(compact('url', 'method', 'class'))
                        . Html::btn('<i class="fa-solid fa-user-plus"></i>', ['class' => 'btn btn-sm btn-success w-100', 'type' => 'submit', 'title' => 'Aggiungi utente'])
                        . Form::close();
                    $class = 'btn btn-sm btn-outline-warning';
                    $body = Html::div('Crea un nuovo utente in riferimento al contatto corrente.<hr>' . $generate, ['class' => 'alert alert-warning']);
                } else {
                    $class = 'btn btn-sm btn-outline-danger';
                    $body = Html::div('È necessario compilare il campo e-mail nel contatto', ['class' => 'alert alert-danger']);
                }

                $icon = '<i class="fa-solid fa-user-slash"></i>';

                break;
            default:
                $class = 'btn btn-sm btn-outline-success';
                $icon = '<i class="fa-solid fa-user-check"></i>';
                $body = UserStatus::generate($this->col);
                break;
        }

        $modalId = "contactUserStatus-" . $this->item->id;
        $string = "Stato utente";
        ${"data-bs-toggle"} = "modal";
        ${"data-bs-target"} = "#" . $modalId;
        $html .= Html::div(
            Html::btn($icon, compact('class', "data-bs-toggle", "data-bs-target")),
            ['class' => 'btn-group']
        );

        $header = Html::tag('h5', $string, ['class' => 'modal-title']) .
            Html::btn(null, ['class' => 'btn-close', 'data-bs-dismiss' => "modal", 'aria-label' => "Close"]);

        $content = Html::div(
            Html::div($header, ['class' => 'modal-header'])
                . Html::div($body ?? null, ['class' => 'modal-body']),
            ['class' => 'modal-content']
        );
        $dialog = Html::div($content, ['class' => 'modal-dialog']);
        $modal = Html::div($dialog, ['class' => 'modal fade', 'id' => $modalId, 'tabindex' => -1, 'aria-hidden' => 'true']);

        $html .= $modal;

        return $html;
    }

    function colPermissions()
    {
        $list = $this->col->map(function ($i) {
            $html = Html::tag(
                'li',
                $i->eti,
                ['class' => 'list-group-item p-1']
            );
            return compact('html');
        })->implode('html');
        return Html::tag('ul', $list, ['class' => 'list-group']);
    }

    function actions()
    {
        return [
            'create' => 'nabre.manage.contact.create',
            'edit' => 'nabre.manage.contact.edit',
            'destroy' => 'nabre.manage.contact.destroy',
        ];
    }

    function query()
    {
        return  $this->model::with(['user', 'permissions'])->get()->sortBy('fullname')->values();
    }
}
