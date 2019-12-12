<?php

namespace BrandStudio\Apie\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use BrandStudio\Apie\Apie;


class ApieController extends Controller
{

    public function index(Request $request, string $model)
    {
        $response = Apie::model($model)
                        ->select($request->toArray())
                        ->get($request->pagination ?? []);

        return response()->json($response);
    }

    public function show(Request $request, string $model, $id)
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
        $req = $request->toArray();
        $response = Apie::model($model)
                        ->select($req)
                        ->get($request->pagination ?? []);

        unset($req['where']);
        unset($req['with']);
        unset($req['pagination']);
        if ($response instanceof Collection) {
            $response = $response->each(function($item) use($req) {
                $item->update($req);
            });
        } else {
            $response = $response->update($req);
        }
        return response()->json($response);
    }

    public function delete(Request $request, string $model)
    {
        $response = Apie::model($model)->delete($request->toArray());
        return response()->json($response);
    }

    public function search(Request $request, string $model)
    {
        $response = Apie::model($model)->search($request->q)->get();
        return response()->json($response);
    }

    public function globalSearch(Request $request)
    {
        return response()->json($request);
    }

}
