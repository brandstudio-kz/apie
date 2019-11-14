<?php

namespace BrandStudio\Apie\Traits\Operations;

trait Update
{

    public static function applyUpdate(&$query, array $data)
    {
        $query->update($data);
    }

}
