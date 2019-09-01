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

        $query = $this->class::apieQuery();

        $filters = $request->all();
        unset($filters['levels']);
        $query->where($filters);

        $query->level($this->levels);

        $per_page = $request->per_page ?? $query->count();
        return $query->paginate($per_page);
    }

    public function show(Request $request, $id)
    {
        $this->init($request);

        $query = $this->class::apieQuery();
        $query->where('id', $id);
        $query->level($this->levels);

        return $query->firstOrFail();
    }

    public function store(Request $request)
    {
        $this->init($request);
        return $this->class::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $query = $this->class::apieQuery();
        $query->where('id', $id);
        $model = $query->firstOrFail();
        return $model->update($request->all());
    }

    public function destroy(Request $request, $id)
    {
        $this->init($request);
        $query = $this->class::apieQuery();
        $query->where('id', $id);
        $model = $query->firstOrFail();
        return $model->delete();
    }

    public function documentation(Request $request)
    {
        $data = [];
        $models = config('apie.models');
        foreach($models as $model) {
            $class = $this->getClassPath($model);
            $data[$model] = $class::getApieLevelsParsed();
        }

        return view('apie.documentation', ['data' => $data]);
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
