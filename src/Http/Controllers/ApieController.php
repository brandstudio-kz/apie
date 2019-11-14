<?php

namespace BrandStudio\Apie\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use BrandStudio\Apie\Apie;

class ApieController extends Controller
{

    public function index(Request $request, string $model)
    {
        $response = Apie::model($model)
                        ->select($request->toArray())
                        ->get();

        return response()->json($response);
    }

    public function show(Request $request, string $model, int $id)
    {
        $response = Apie::model($model)
                        ->select($request->toArray())
                        ->find($id);

        return response()->json($response);
    }

    public function store(Request $request, string $model)
    {
        $response = Apie::model($model)->insert($request->toArray());
        return response()->json($response);
    }

    public function update(Request $request, string $model)
    {
        return response()->json([$model, $id]);
    }

    public function delete(Request $request, string $model, int $id)
    {
        return response()->json([$model, $id]);
    }

    public function search(Request $request, string $model)
    {
        return response()->json([$model]);
    }

    public function globalSearch(Request $request)
    {
        return response()->json($request);
    }

}
