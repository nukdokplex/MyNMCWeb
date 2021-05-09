<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Http\Request;

class SpecializationsController extends Controller
{
    public function index($specialization = null){
        if (!$specialization){
            return view('specializations.index');
        }
        else {
            $specialization = Specialization::query()->where('id', '=', $specialization)->firstOrFail();

            return view('specializations.groups', ['specialization' => $specialization]);

        }

    }

    public function specializations_ajax(){
        return response()->json(Specialization::all());
    }

    public function specialization_ajax($specialization){
        $specialization = Specialization::query()->where('id', '=', $specialization)->firstOrFail();

        return response()->json($specialization);
    }

    public function delete_specialization_ajax(Request $request){
        \Validator::make($request->all(), [
            'id' => 'required|integer'
        ])->validate();

        Specialization::query()->where('id', '=', $request->input('id'))->firstOrFail()->delete();

        return response(null); //return ok
    }

    public function update_specialization_ajax(Request $request){
        \Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|max:255'
        ])->validate();

        $specialization = Specialization::query()->where('id', '=', $request->input('id'))->firstOrFail();

        if (!$specialization){
            return response(null, 400)->json(
                [
                    'status' => 'error',
                    'message' => 'Специальность с этим ID не найдена.'
                ]
            );
        }

        $specialization->name = $request->input('name');
        $specialization->description = $request->input('description');
        $specialization->saveOrFail();

        return response()->json($specialization);

    }

    public function create_specialization_ajax(Request $request){
        \Validator::make($request->all(), [
            'name' => 'required|max:255'
        ])->validate();

        $specialization = new Specialization();
        $specialization->name = $request->input('name');
        $specialization->description = $request->input('description');
        $specialization->save();

        return response()->json($specialization);
    }

    public function update_specialization_groups_ajax(Request $request, $group){
        $groups = $request->json('groups');

        if (!$groups){
            return response(null, 400)->json(
                [
                    'status' => 'error',
                    'message' => 'Поле groups обязательно!'
                ]
            );
        }

        $specialization = Specialization::query()->where('id', '=', $group)->firstOrFail();

        $groups_to_add = [];

        foreach ($groups as $group){
            if (!array_key_exists('id', $group)){
                continue;
            }

            array_push($groups_to_add, Group::query()->where('id', '=', $group['id'])->firstOrFail());
        }

        $specialization->setGroups($groups_to_add);

        return response()->json($specialization->groups()->get());
    }

    public function specialization_groups_ajax($specialization){
        $specialization = Specialization::query()->where('id', '=', $specialization)->firstOrFail();

        return response()->json($specialization->groups()->get());
    }
}
