<?php

namespace BrandStudio\Apie\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;

class ApieController extends Controller
{

    const DEFAULT_LEVEL = 's';

    public function resource(Request $request)
    {
        $class = 'App\\' . studly_case(str_singular($request->table));

        if (!class_exists($class)) {
            return abort(400, "Invalid request class {$class} not found!");
        }

        $query = $class::query();

        $levels = $request->levels ?? self::DEFAULT_LEVEL;

        if ($request->id) {
            $query->where('id', $request->id);
        }

        $query->level($levels);

        $per_page = $request->per_page ?? $query->count();
        return $per_page>1 ? $query->paginate($per_page) : $query->get();
    }

    public function documentation(Request $request)
    {
        $data = [
            'base_url' => 'http://api.only.local.com',
            'info' => '',
            'filter' => '',
        ];

        $models = ['Product', 'Category', 'Brand'];
        foreach($models as $model) {
            $data[$model] = ("App\\{$model}")::LEVELS;
        }

        return $data;
    }

}
