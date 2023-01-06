<?php

namespace Nabre\Repositories\FormTwo\FormTrait;

use Nabre\Repositories\FormTwo\Field;

trait Output
{
    static $ruleOutput = ['email' => Field::TEXT, 'password' => [Field::PASSWORD2, Field::PASSWORD]];

    private function output()
    {
        $output = $this->getItemData('output');
        $type = $this->getItemData('type');
        $rules = $this->getItemData('set.rules.fn', []);
        $enabled = collect([]);

        if ($type != 'fake') {
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
                    switch ($this->getItemData('set.rel.type')) {
                        case "BelongsTo":
                        case "HasOne":
                            $enabled = $enabled->push(Field::SELECT);
                            break;
                        case "BelongsToMany":
                        case "HasMany":
                            $enabled = $enabled->merge([Field::CHECKBOX, Field::SELECT]);
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

            $enabled = $enabled->push(Field::STATIC)->push(Field::HIDDEN)->unique()->values();

            if (!$enabled->filter(fn ($str) => $str == $output)->count() && $enabled->count()) {
                $output = $enabled->first();
            }
        }

        $this->setItemData('output', $output ?? Field::STATIC, true);
    }
}
