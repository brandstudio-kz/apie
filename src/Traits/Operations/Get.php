<?php

namespace BrandStudio\Apie\Traits\Operations;

trait Get
{

    public static function applyGet(&$query, array $data)
    {
        static::applyFilters($query, $data['where'] ?? []);
        static::applyOrderBy($query, $data['order'] ?? []);
        static::applySelect($query, $data['select'] ?? '*');
        static::applyWith($query, $data['with'] ?? []);
    }

    public static function applySelect(&$query, $select)
    {
        $query->select($select);
    }

    public static function applyWith(&$query, $fields)
    {
        foreach($fields as $key => $field) {
            $with = 'with';
            if (isset($field['count'])) {
                if ($field['count']) {
                    $with = 'withCount';
                }
                unset($field['count']);
            }

            if (is_string($key)) {
                if (is_array($field) && count($field)) {
                    $query->{$with}([$key => function($q) use($field) {
                        static::applyGet($q, $field);
                    }]);
                } else {
                    $query->{$with}($key);
                }
            } else if (is_string($field)) {
                $query->{$with}($field);
            }
        }
    }

    public static function applyOrderBy(&$query, array $order)
    {
        foreach($order as $key => $direction) {
            if (is_numeric($key)) {
                $query->orderBy($direction);
            } else {
                $query->orderBy($key, $direction);
            }
        }

    }

    public static function applyFilters(&$query, $filters, $key = null)
    {
        foreach($filters as $key => $filter) {
            if (is_string($key)) {
                if (!is_array($filter)) {
                    $query->where($key, $filter);
                } else {
                    if (!is_array($filter[0]) && (array_keys($filter) === range(0, count($filter) - 1) || array() === $filter)) {// If array is not associative
                        $query->whereIn($key, $filter);
                    } else {
                        $query->whereHas($key, function($q) use($filter) {
                            $q->where(function($q) use($filter) {
                                static::applyFilters($q, $filter);
                            });
                        });
                    }
                }
            } else {
                $query->orWhere(function($q) use($filter) {
                    static::applyFilters($q, $filter);
                });
            }
        }
    }


}
