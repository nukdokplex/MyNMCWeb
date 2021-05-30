<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function React\Promise\all;

class GroupsController extends Controller
{
    public function groups_ajax(){
        return response()->json(Group::all());
    }



    public function create_group_ajax(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'description' => 'max:255'
        ]);



        $group = new Group();
        $group->name = $request->input('name');
        $group->description = $request->input('description');
        $group->save();

        return response()->json($group);


    }

    public function update_group_ajax(Request $request){
        \Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|max:100'
        ])->validate();

        $group = Group::query()->where('id', '=', $request->input('id'))->firstOrFail();

        if (!$group){
            return response(null, 400)->json(
                [
                    'status' => 'error',
                    'message' => 'Группа с этим ID не найдена.'
                ]
            );
        }

        $group->name = $request->input('name');
        $group->description = $request->input('description');
        $group->saveOrFail();

        return response()->json($group);

    }

    public function delete_group_ajax(Request $request){
        \Validator::make($request->all(), [
            'id' => 'required|integer'
        ])->validate();



        Group::query()->where('id', '=', $request->input('id'))->firstOrFail()->delete();

        return response(null); //return ok
    }

    public function group_users_ajax($group){
        return response()->json(
            Group::query()->where("id", "=", $group)->firstOrFail()->users()->select('id')->get()->pluck('id')
        );
    }

    public function update_group_users_ajax(Request $request, $group){
        $users = $request->json('users');

        if (!$users){
            return response(null, 400)->json(
                [
                    'status' => 'error',
                    'message' => 'Поле users обязательно!'
                ]
            );
        }

        $group = Group::query()->where('id', '=', $group)->firstOrFail();

        $users_to_add = [];

        foreach ($users as $user){
            if (!array_key_exists('id', $user)){
                continue;
            }

            array_push($users_to_add, User::query()->where('id', '=', $user['id'])->firstOrFail());
        }

        $group->setUsers($users_to_add);

        return response()->json($group->users()->get());
    }



    public function index($group = null){
        if ($group){
            $group = Group::query()->where('id', '=', $group)->firstOrFail();

            return view("groups.users", ['group' => $group]);
        }
        else{
            return view("groups.index");
        }
    }

    public function api_groups(){
        $groups = Group::all()->makeHidden('description');

        return response()->json($groups, 200);
    }

}
