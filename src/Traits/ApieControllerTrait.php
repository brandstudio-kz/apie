<?php

namespace BrandStudio\Apie\Traits;
use Illuminate\Http\Request;

trait ApieControllerTrait
{

    protected $class_name;
    protected $class;
    protected $levels;

    private function init(Request $request)
    {
        $this->class_name = $this->getClassName($request->table);
        $this->class = $this->getClassPath($this->class_name);
        $this->validateModel($this->class_name, $this->class);

        $this->levels = $request->levels ?? config('apie.default_level');
    }

    public function index(Request $request)
    {
        $this->init($request);
        $order_by = $request->order_by ?? 'id';
        $order = $request->order ?? 'asc';

        $query = $this->class::apieQuery();

        $filters = $request->all();
        unset($filters['levels']);
        unset($filters['per_page']);
        unset($filters['page']);
        unset($filters['order_by']);
        unset($filters['order']);
        unset($filters['has']);
        foreach($filters as $key => $filter) {
            if (is_array($filter)) {
                $query->whereIn($key, array_values($filter));
            } else if (in_array(substr($key, -1), ['>', '<'])) {
                $query->where(substr($key, 0, -1), substr($key, -1), $filter);
            } else if (in_array(substr($key, -2), ['>=', '<='])) {
                $query->where(substr($key, 0, -2), substr($key, -2), $filter);
            } else if (substr($key, -2) == '!=') {
                $query->where(function($q) {
                    $q  ->where($key, '!=', $filter)
                        ->orWhereNull(substr($key, 0, -2));
                });
            } else {
                $query->where($key, $filter);
            }
        }

        $hasFilters = $request->has ?? [];
        foreach($hasFilters as $has) {
            if (json_decode($has)) {
                $rel = json_decode($has, true);
                foreach($rel as $key=>$value) {
                    $query->whereHas($key, function($query) use($value) {
                        if (is_array($value)) {
                            $query->whereIn('id', $value)->apie();
                        }
                        else {
                            $query->where('id', $value)->apie();
                        }
                    });
                }
            }
            else {
                $query->whereHas($has, function($query) {
                    $query->apie();
                });
            }
        }

        $query->level($this->levels);
        $query->orderBy($order_by, $order);

        $per_page = $request->per_page ?? $query->count();
        return response()->json($query->paginate($per_page));
    }

    public function show(Request $request, $table, $id)
    {
        $this->init($request);

        $query = $this->class::apieShowQuery();
        $query->where('id', $id);
        $query->level($this->levels);

        return response()->json($query->firstOrFail());
    }

    public function store(Request $request)
    {
        $this->init($request);
        $model = $this->class::create($request->all());
        return response()->json($model);
    }

    public function update(Request $request, $table, $id)
    {
        $this->init($request);
        $query = $this->class::apieQuery();
        $query->where('id', $id);
        $model = $query->firstOrFail();
        $model->update($request->all());
        return response()->json($model);
    }

    public function destroy(Request $request, $table, $id)
    {
        $this->init($request);
        $query = $this->class::apieQuery();
        $query->where('id', $id);
        $model = $query->firstOrFail();
        $model->delete();
        return response()->json(null, 204);
    }

    public function documentation(Request $request)
    {
        return view('brandstudio::apie.documentation');
    }

    public function documentationRaw(Request $request)
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
        return \Str::studly(\Str::singular($name));
    }

}
