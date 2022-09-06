<?php

namespace Nabre\Database\Eloquent;

use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Jenssegers\Mongodb\Eloquent\Model as JModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Jenssegers\Mongodb\Eloquent\HybridRelations; #Per relazioni ibride tra SQL & MONGO
use Nabre\Database\Eloquent\RelationshipsTrait;
use Nabre\Database\Eloquent\CascadeTrait;
use Nabre\Traits\TableName;

class Model extends JModel
{
    use HasFactory;
    use RelationshipsTrait;
    use RecursiveSaveTrait;
    use HasEvents;
    /*  use CascadeTrait;
    use CopyTrait;
    use TableName;
    use ModelDataTrait;
    use CastsTrait;*/

    protected $guard_name = 'web';
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s', 'updated_at' => 'datetime:Y-m-d H:i:s'];
    protected $guarded = ['_id'];

    var $defaultAttributes;
    var $frontVariables = [];
    protected $relationships;
}
