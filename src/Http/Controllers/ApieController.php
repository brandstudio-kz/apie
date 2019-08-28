<?php

namespace BrandStudio\Apie\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;

class ApieController extends Controller
{

    public function resource(Request $request)
    {
        $class_name = $this->getClassName($request->table);
        $class = $this->getClassPath($class_name);

        $this->validateModel($class_name, $class);

        $query = $class::apieQuery();

        $levels = $request->levels ?? config('apie.default_level');

        $get_method = 'get';

        if ($request->id) {
            $query->where('id', $request->id);
            $get_method = 'firstOrFail';
        }

        $query->level($levels);

        $per_page = $request->per_page ?? $query->count();
        return $per_page>1 ? $query->paginate($per_page) : $query->{$get_method}();
    }

    public function documentation(Request $request)
    {
        $data = [];
        $models = config('apie.models');
        foreach($models as $model) {
            $class = $this->getClassPath($model);
            $data[$model] = $class::getApieLevelsParsed();
        }

        return $data;
    }

    private function validateModel(string $model, string $class)
    {
        if (!class_exists($class) || !in_array($model, config('apie.models'))) {
            return abort(400, "Invalid request class {$class} not found!");
        }
    }

    private function getClassPath(string $model) : string
    {
        return config('apie.models_path') . $model;
    }

    private function getClassName(string $name) : string
    {
        return studly_case(str_singular($name));
    }

}
