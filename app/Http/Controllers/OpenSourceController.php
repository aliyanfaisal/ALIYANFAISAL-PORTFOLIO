<?php

namespace App\Http\Controllers;

class OpenSourceController extends Controller
{
    public function index()
    {
        return view('open-source.index', ['projects' => config('opensource')]);
    }

    public function show(string $slug)
    {
        $project = config("opensource.$slug");

        abort_if($project === null, 404);

        return view('open-source.show', [
            'project' => $project,
            'others' => collect(config('opensource'))->except($slug)->all(),
        ]);
    }
}
