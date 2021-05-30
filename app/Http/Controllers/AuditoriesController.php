<?php

namespace App\Http\Controllers;

use App\Models\Auditory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuditoriesController extends Controller
{
    public function index(){
        return view('auditories.index');
    }

    public function auditories_ajax(){
        return response()->json(Auditory::all());
    }

    public function create_auditory_ajax(Request $request){
        $validator = \Validator::make($request->json()->all(), [
            'name' => [
                'required',
                'min:1',
                'max:100'
            ]
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        }

        $auditory = new Auditory();
        $auditory->name = $request->json('name');
        $auditory->saveOrFail();

        return response()->json($auditory);
    }

    public function update_auditory_ajax(Request $request){
        $validator = \Validator::make($request->json()->all(), [
            'id' => [
                'required',
                'numeric',
                Rule::exists('App\\Models\\Auditory', 'id')
            ],
            'name' => [
                'required',
                'min:1',
                'max:100'
            ]
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        }
        try {
            $auditory = Auditory::query()->where('id', '=', $request->json('id'))->firstOrFail();
        }
        catch (ModelNotFoundException $e){
            abort(404);
        }

        $auditory->name = $request->json('name');
        $auditory->saveOrFail();

        return response()->json($auditory);
    }

    public function delete_auditory_ajax(Request $request){
        $validator = \Validator::make($request->json()->all(), [
            'id' => [
                'required',
                'numeric',
                Rule::exists('App\\Models\\Auditory', 'id')
            ]
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        }
        try {
            $auditory = Auditory::query()->where('id', '=', $request->json('id'))->firstOrFail();
        }
        catch (ModelNotFoundException $e){
            abort(404);
        }

        $auditory->delete();

        return response('ok!');
    }

    public function api_auditories(){
        $auditories = Auditory::all()->makeHidden('description');

        return response()->json($auditories, 200);
    }
}
