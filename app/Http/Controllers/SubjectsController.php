<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    public function index(){
        return view('subjects.index');
    }

    public function subjects_ajax(){
        return response()->json(Subject::all());
    }

    public function delete_subject_ajax(Request $request){
        $validator = \Validator::make($request->json()->all(), [
            'id' => 'required|numeric|exists:App\\Models\\Subject,id'
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }
        try {
            Subject::query()->where('id', '=', $request->json("id"))->delete();
        }
        catch (\Exception $e){
            return response()->json(['status' => 400, 'errors' => "К сожалению, действие невозможно, т.к. данная строка используется другими сущностями. Действие приведет к неработоспособности системы."], 400);
        }

        return response('ok!');
    }

    public function create_subject_ajax(Request $request){

        $validator = \Validator::make($request->json()->all(), [
            'name' => 'required|max:255',
            'description' => ''
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $subject = new Subject();

        $subject->name = $request->json('name');
        $subject->description = $request->json('description');
        $subject->saveOrFail();

        return response()->json($subject);
    }

    public function update_subject_ajax(Request $request){

        $validator = \Validator::make($request->json()->all(), [
            'id' => 'required|numeric|exists:App\\Models\\Subject,id',
            'name' => 'required|max:255',
            'description' => ''
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $subject = Subject::query()->where('id', '=', $request->json('id'))->firstOrFail();

        $subject->name = $request->json('name');
        $subject->description = $request->json('description');
        $subject->saveOrFail();

        return response()->json($subject);
    }
}
