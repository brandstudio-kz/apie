<?php

namespace BrandStudio\Apie;

use Illuminate\Database\Eloquent\Model as OriginalModel;
use BrandStudio\Apie\Traits\ApieModelTrait;

abstract class Model extends OriginalModel
{
    use ApieModelTrait;

    abstract public static function getApieRelations() : array;
    abstract public static function getApieLevels() : array;

}
