<?php

namespace Nabre\Services\Html;

use App\Models\User;
use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;

class UserStatus
{
    static function generate(User $user)
    {
        $list = self::item('e-mail', $user->email) .
            self::item('password', null, !is_null($user->password), 'L\'utente deve generare una nuova password.') .
            self::item('email confirmad', null, !is_null($user->email_verified_at), 'L\'utente deve confermare l\'e-mail tramite il messagio inviato.') .
            self::item('enabled', null, !$user->disabled, 'L\'amministratore ha i privilegii per abilitare il presente utente bloccato.');
        return Html::tag('ul', $list, ['class' => 'list-group']);
    }

    static function item($name, $value = null, $bool = null, $text = null)
    {
        $content = '';
        if (is_null($bool)) {

            $color = '';
            $icon = '<i class="fa-solid fa-minus"></i>';
        } else {
            switch ($bool) {
                case true:
                    $color = 'text-success';
                    $icon = '<i class="fa-regular fa-circle-check"></i>';
                    break;
                case false:
                    $color = 'text-danger';
                    $icon = '<i class="fa-regular fa-circle-xmark"></i>';
                    break;
            }
        }

        $content .= Html::div(
            Html::div($icon, ['class' => $color]),
            ['class' => 'col-auto']
        );
        $content .= Html::div($name, ['class' => 'col']);
        if (!$bool && !is_null($bool) && !is_null($text)) {
            $value = Html::div($text, ['class' => 'alert alert-info p-1 m-0']);
        }
        $content .= Html::div($value, ['class' => 'col']);
        $row = Html::div($content, ['class' => 'row']);

        return Html::div($row, ['class' => 'list-group-item']);
    }
}
