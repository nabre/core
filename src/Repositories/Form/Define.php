<?php

namespace Nabre\Repositories\Form;

use Nabre\Casts\CkeditorCast;
use Nabre\Casts\LocalCast;
use Nabre\Casts\SettingTypeCast;
use Nabre\Repositories\Form\Field;

class Define
{
    static $requestOutput = ['email' => Field::TEXT];

    static function outputType(&$output, $type, $cast, $set, $request, $view)
    {
        $enabled = [];
        #tipo di variabile
        switch ($type) {
            case "fillable":
                switch ($cast) {
                    case PasswordCast::class:
                        $enabled = Field::PASSWORD;
                        break;
                    case LocalCast::class:
                        $enabled = Field::TEXT_LANG;
                        break;
                    case SettingTypeCast::class:
                        $enabled = Field::FIELD_TYPE_LIST;
                        break;
                    case "boolean":
                        $enabled = Field::BOOLEAN;
                        break;
                    case CkeditorCast::class:
                        $enabled=Field::TEXTAREA_CKEDITOR;
                        break;
                    default:
                        $enabled = [Field::TEXT, Field::TEXTAREA,Field::TEXTAREA_CKEDITOR, Field::HIDDEN];
                        collect(self::$requestOutput)->each(function ($en) use (&$enabled) {
                            $enabled = array_unique(array_merge((array)$en, (array)$enabled));
                        });
                        break;
                }
                break;
            case "attribute":
                $enabled = Field::STATIC;
                break;
            case "relation":
                switch ($set['rel']->type) {
                    case "BelongsTo":
                    case "HasOne":
                        $enabled = Field::SELECT;
                        break;
                    case "BelongsToMany":
                    case "HasMany":
                        $enabled = [Field::CHECKBOX,Field::SELECT];
                        break;
                    case "EmbedsMany":
                        $enabled = Field::EMBEDS_MANY;
                        break;
                    case "EmbedsOne":
                        $enabled = Field::EMBEDS_ONE;
                        break;
                }
                break;
        }

        #requests definizione
        collect(self::$requestOutput)->each(function ($e, $r) use (&$enabled, $request) {
            if (in_array($r, $request)) {
                $enabled = array_values(array_unique(array_intersect((array)$e, (array)$enabled)));
            }
        });

        $output = $output ?? null;
        $enabled = collect((array)$enabled)->push(Field::STATIC)->push(Field::HIDDEN)->unique()->values()->toArray();
        if (!in_array($output, $enabled) && $type != 'fake') {
            $default = collect($enabled)->first();
        }

        $output = ($view ? Field::STATIC : null) ?? ($default ?? null) ?? $output ?? Field::STATIC;
    }
}
