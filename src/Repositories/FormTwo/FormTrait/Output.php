<?php

namespace Nabre\Repositories\FormTwo\FormTrait;

use Nabre\Repositories\FormTwo\Field;
use Nabre\Repositories\FormTwo\FormConst;

trait Output
{
    static $ruleOutput = ['email' => Field::TEXT, 'password' => [Field::PASSWORD2, Field::PASSWORD]];

    private function output()
    {
        $output = $this->getItemData(FormConst::OUTPUT);
        $type = $this->getItemData(FormConst::TYPE);
        $rules = $this->getItemData('set.rules.fn', []);
        $enabled = collect([]);

        if ($type != 'fake') {
            if ($this->getItemData(FormConst::VARIABLE) == $this->collection->getKeyName()) {
                $enabled = $enabled->merge([Field::HIDDEN]);
            } else {
                switch ($type) {
                    case false:
                        break;
                    case "fillable":
                        switch ($this->getItemData('cast')) {
                            case PasswordCast::class:
                                $enabled = $enabled->push(Field::PASSWORD);
                                break;
                            case LocalCast::class:
                                $enabled = $enabled->push(Field::TEXT_LANG);
                                break;
                            case SettingTypeCast::class:
                                $enabled = $enabled->push(Field::FIELD_TYPE_LIST);
                                break;
                            case "boolean":
                                $enabled = $enabled->push(Field::BOOLEAN);
                                break;
                            case CkeditorCast::class:
                                $enabled = $enabled->push(Field::TEXTAREA_CKEDITOR);
                                break;
                            default:
                                $ruleEnable = array_intersect($rules, array_keys(self::$ruleOutput));
                                if (count($ruleEnable)) {
                                    collect(self::$ruleOutput)->filter(fn ($v, $k) => in_array($k, $ruleEnable))->each(function ($fieldType) use (&$enabled) {
                                        $enabled = $enabled->merge((array)$fieldType);
                                    });
                                } else {
                                    $enabled = $enabled->merge([Field::TEXT, Field::TEXTAREA, Field::TEXTAREA_CKEDITOR, Field::HIDDEN]);
                                }
                                break;
                        }
                        break;
                    case "attribute":
                        $enabled = $enabled->push(Field::STATIC);
                        break;
                    case "relation":
                        switch ($this->getItemData(FormConst::REL_TYPE)) {
                            case "BelongsTo":
                            case "HasOne":
                                $enabled = $enabled->merge([Field::SELECT, Field::RADIO]);
                                break;
                            case "BelongsToMany":
                            case "HasMany":
                                $enabled = $enabled->merge([Field::CHECKBOX, Field::SELECT_MULTI]);
                                break;
                            case "EmbedsMany":
                                $enabled = $enabled->push(Field::EMBEDS_MANY);
                                break;
                            case "EmbedsOne":
                                $enabled = $enabled->push(Field::EMBEDS_ONE);
                                break;
                        }
                        break;
                }
            }

            $enabled = $enabled->push(Field::STATIC)->push(Field::HIDDEN)->unique()->values();

            if (!$enabled->filter(fn ($str) => $str == $output)->count() && $enabled->count()) {
                $output = $enabled->first();
            }
        }

        $this->setItemData(FormConst::OUTPUT, $output ?? Field::STATIC, true);

        #prepara le informazione necessarie per livewire FormEmbed
        if (in_array($this->getItemData(FormConst::OUTPUT), [Field::EMBEDS_MANY, Field::EMBEDS_ONE])) {
            $this->setItemData('embed.parent.model', $this->model, true);
            $this->setItemData('embed.parent.dataKey', data_get($this->data, $this->data->getKeyName()), true);
            $this->setItemData('embed.parent.variable', $this->getItemData('variable'), true);
            $this->setItemData('embed.wire.output', $this->getItemData('output'), true);
            $this->setItemData('embed.wire.model', $this->getItemData('set.rel.model'), true);
        }
        return $this;
    }
}
