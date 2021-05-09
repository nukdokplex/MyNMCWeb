<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Translates hardcoded role names to russian
     *
     * @param string $name
     * @return string
     */
    public static function translateRoleName(string $name): string
    {
        $translations = [
            'guest' => 'Гость',
            'student' => 'Студент',
            'teacher' => 'Преподаватель',
            'administrator' => 'Администрация',
            'system architect' => 'Системный архитектор'
        ];

        if (array_key_exists($name, $translations)){
            return $translations[$name];
        }
        return $name;
    }

    protected function getAvailableRoles($role){
        $available_roles = [];

        if ($role->name == 'administrator'){
            $available_roles = [
                Role::findByName('guest'),
                Role::findByName('student'),
                Role::findByName('teacher')
            ];
        }
        if ($role->name == 'system architect'){
            $available_roles = Role::all()->toArray();
        }

        return $available_roles;
    }

    public function index()
    {
        $available_roles = $this->getAvailableRoles(auth()->user()->roles()->firstOrFail());
        $result_roles = [];
        foreach ($available_roles as $available_role){
            $result_roles[$available_role['id']] = self::translateRoleName($available_role['name']);
        }


        return view('users.index', ['available_roles' => $result_roles]);
    }

    /**
     * Returns all users
     */
    public function users_ajax(){
        return response()->json(User::all());
    }

    public function users_detailed_ajax(){
        $users = User::all()->makeVisible(['password']);
        $users_to_return = [];
        foreach ($users as $user) {
            array_push($users_to_return, [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => '',
                'role' => $user->roles()->firstOrFail()->id,
                'created_at' => Carbon::parse($user->created_at)->format('d.m.Y H:i:s'),
                'updated_at' => Carbon::parse($user->updated_at)->format('d.m.Y H:i:s'),
            ]);
        }

        return response()->json($users_to_return);
    }

    public function users_by_role_ajax(Request $request){
        $validator = Validator::make($request->all(), [
            'roles' => 'required|max:100'
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }
        $data = ['roles' => []];

        foreach (explode(',', $request->input('roles')) as $role){
            array_push($data['roles'], $role);
        }

        $validator = Validator::make($data, [
            'roles' => 'required|array|min:1|max:4',
            'roles.*' => [
                'required',
                'filled',
                'max:50',
                'min:2',
                Rule::exists('roles', 'name')
            ]
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $users = [];

        foreach ($data['roles'] as $role){
            $users_to_add = Role::findByName($role)->users()->get()->toArray();

            for ($i = 0; $i < count($users_to_add); $i++){
                $users_to_add[$i]['role'] = self::translateRoleName($role);
            }

            $users = array_merge($users, $users_to_add);
        }

        return response()->json($users);
    }

    public function users_create_ajax(Request $request){
        $validator = \Validator::make($request->json()->all(), [
            'name' => 'required|max:100',
            'email' => 'required|max:255|email:rfc,dns,spoof',
            'password' => 'required|password:api|max:50|min:8',
            'role' => [
                'required',
                'numeric',
                Rule::exists('Spatie\\Permission\\Models\\Role', 'id'),
                Rule::in(array_column($this->getAvailableRoles(auth()->user()->roles()->firstOrFail()), 'id'))
            ]
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $user = new User();

        $user->name = $request->json('name');
        $user->email = $request->json('email');
        $user->markEmailAsVerified();

        $user->password = Hash::make($request->json('password'));

        $user->save();

        $user->assignRole([Role::findById($request->json('role'))]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => '',
            'role' => $user->roles()->firstOrFail()->id,
            'created_at' => Carbon::parse($user->created_at)->format('d.m.Y H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('d.m.Y H:i:s'),
        ]);
    }

    public function user_update_ajax(Request $request){
        $validator = \Validator::make($request->json()->all(), [
            'id' => [
                'required',
                'numeric',
                Rule::exists('App\\Models\\User', 'id')
            ],
            'name' => 'required|max:100',
            'email' => 'required|max:255|email:rfc,dns,spoof',
            'password' => 'required|password:api|max:50|min:8',
            'role' => [
                'required',
                'numeric',
                Rule::exists('Spatie\\Permission\\Models\\Role', 'id'),
                Rule::in(array_column($this->getAvailableRoles(auth()->user()->roles()->firstOrFail()), 'id'))
            ]
        ]);
        $is_password = true;

        if ($validator->fails()){
            $errors = $validator->errors();

            if (count($errors->messages()) == 1 and array_key_exists('password', $errors->messages())){
                $is_password = false;
            }
            else {
                return response()->json(['status' => 400, 'errors' => $errors], 400);
            }
        }

        try{
            $user = User::query()->where('id', '=', $request->json('id'))->firstOrFail();
        }
        catch (ModelNotFoundException $exception){
            abort(404);
        }

        $user->name = $request->json('name');
        $user->email = $request->json('email');
        $user->markEmailAsVerified();

        if ($is_password) $user->password = Hash::make($request->json('password'));

        $user->save();

        $user->assignRole([Role::findById($request->json('role'))]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => $request->json('password'),
            'role' => $user->roles()->firstOrFail()->id,
            'created_at' => Carbon::parse($user->created_at)->format('d.m.Y H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('d.m.Y H:i:s'),
        ]);
    }

    public function user_delete_ajax(Request $request){
        $validator = \Validator::make($request->json()->all(), [
            'id' => [
                'required',
                'numeric',
                Rule::exists('App\\Models\\User', 'id')
            ]
        ]);
        $is_password = true;

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        try{
            $user = User::query()->where('id', '=', $request->json('id'))->firstOrFail();
        }
        catch (ModelNotFoundException $exception){
            abort(404);
        }

        $user->roles()->firstOrFail()->id;
        $this->getAvailableRoles(auth()->user()->roles()->firstOrFail());
        array_column(
            $this->getAvailableRoles(auth()->user()->roles()->firstOrFail()), 'id'
        );
        if (!in_array(
            $user->roles()->firstOrFail()->id,
            array_column(
                $this->getAvailableRoles(auth()->user()->roles()->firstOrFail()), 'id'
            )
        )
        ){
            return response()->json(['status' => 419, 'errors' => ['id' => 'Пользователь с этим ИД имеет непостижимые для вас привилегии']], 419);
        }

        $user->delete();

        return response('ok!');

    }



}
