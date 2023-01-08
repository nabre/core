<?php

namespace Nabre\Repositories\FormTwo\Livewire;

use Nabre\Repositories\FormTwo\Field;
use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;

trait Embed
{

    static $findEmbed='{{[!embed-content]!}}';

    function embedItRemove($param, $id)
    {
        unset($this->wireValues[$param][$id]);
        $this->wireValues[$param] = array_values($this->wireValues[$param]);
    }

    function embedItAdd($param)
    {
        $this->wireValues[$param][] = $this->embedArray($param);
    }

    private function embedArray($param)
    {
        return [];
        //   $data = $data ?? $this->model::make();
        //  return (new $this->form)->data($data)->embedMode()->values();
    }

    private function htmlEmbedItem($item, $field = null)
    {
        $field=$field??self::$findEmbed;
        return (string) $this->form->itemHtml($item, $field);
    }

    private function embedRenderItem($item)
    {
        $form = data_get($item, 'embed.wire.form');
        $model = data_get($item, 'embed.wire.model');
        $output = data_get($item, 'embed.wire.output');
        $parentVar = data_get($item, 'embed.parent.variable');
        switch ($output) {
            case Field::EMBEDS_MANY:
                $array = data_get($this->wireValues, $parentVar);
                $items = collect($array)->keys()->map(function ($num) use ($form, $model, $parentVar, $array) {
                    $param = $parentVar . "." . $num;
                    $html = '';
                    $html .= (count($array) > 1) ? $this->embedButtonMove() : null;
                    $html .= Html::div($this->generateEmbedItem($form, $model, $param), ['class' => 'col']);
                    $html .= $this->embedButtonRemove($parentVar, $num);

                    return (string) Html::div(Html::div($html, ['class' => 'row']), ['class' => 'list-group-item p-1']);
                })->implode('');

                $items .= $this->embedButtonAdd($parentVar);

                $html = Html::div($items, ['class' => 'list-group']);
                break;
            case Field::EMBEDS_ONE:
                $html = $this->generateEmbedItem($form, $model, $parentVar);
                break;
        }

        return (string) $html;
    }

    private function embedButtonMove()
    {
        $btn = Html::div('<i class="fa-solid fa-grip-vertical"></i>', ['class' => 'btn btn-dark btn-sm h-100']);
        return Html::div($btn, ['class' => 'col-auto']);
    }

    private function embedButtonRemove(string $param, int $num)
    {
        $btn = Html::div(
            '<i class="fa-solid fa-trash-can"></i>',
            [
                'title' => 'Elimina',
                'wire:click' => "embedItRemove('$param',$num)",
                'class' => 'btn btn-danger btn-sm h-100',
            ]
        );
        return Html::div($btn, ['class' => 'col-auto']);
    }

    private function embedButtonAdd($param)
    {
        return Html::tag(
            'button',
            '<i class="fa-regular fa-square-plus"></i>',
            [
                'title' => 'Aggiungi',
                'wire:click' => "embedItAdd('$param')",
                'type' => 'button',
                'class' => " text-center list-group-item list-group-item-action list-group-item-success"
            ]
        );
    }

    private function generateEmbedItem($form, $model, $wire)
    {
        return (new $form($model))->embedMode($wire)->generate();
    }
}
