<?php

namespace App\Http\Controllers;

use App\Models\Auditory;
use App\Models\Group;
use App\Models\PrimarySchedule;
use App\Models\RingsSchedule;
use App\Models\ScheduleSession;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class ScheduleController extends Controller
{

    public function index(){
        $user = auth()->user();
        if ($user == null){
            return redirect()->route('schedule.models', ['model_type' => 'group'], 302);
        }
        try {
            $role = $user->roles()->firstOrFail();
        }
        catch (ModelNotFoundException $e){
            return redirect()->route('schedule.models', ['model_type' => 'group'], 302);
        }

        if ($role->name == 'student'){
            try {
                $group = $user->groups()->firstOrFail();
            }
            catch (ModelNotFoundException $e){
                return redirect()->route('schedule.models', ['model_type' => 'group'], 302);
            }

            return redirect()->route('schedule.model', ['model_type' => 'group', 'model_id' => $group->id], 302);
        }
        else if ($role->name == 'teacher'){
            return redirect()->route('schedule.model', ['model_type' => 'teacher', 'model_id' => $user->id], 302);
        }
        else {
            return redirect()->route('schedule.models', ['model_type' => 'group'], 302);
        }
    }

    public function models($model_type){
        $data = [];
        $data['_model'] = $model_type;
        $teacher = Role::findByName('teacher');
        switch ($model_type){
            case 'group':
                $data['_model_str'] = 'группам';
                $data['models'] = Group::query()
                    ->selectRaw('groups.*, IFNULL(specializations.name, "(нет)") AS specialization')
                    ->fromRaw('groups')
                    ->join('group_has_specialization', 'groups.id', '=', 'group_has_specialization.group_id', 'left outer')
                    ->join('specializations', 'group_has_specialization.specialization_id', '=', 'specializations.id', 'left outer')
                    ->get()->toArray();
                break;
            case 'teacher':
                $data['_model_str'] = 'преподавателям';
                $data['models'] = User::query()
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', 'App\\Models\\User')
                    ->where('model_has_roles.role_id', '=', $teacher->id)
                    ->select('users.id', 'users.name')
                    ->get()->toArray();
                break;
            case 'auditory':
                $data['_model_str'] = 'аудиториям';
                $data['models'] = Auditory::all()->toArray();
                break;
            default: abort(404);
        }

        return view('schedule.models', $data);
    }

    public function schedule($model_type, $model_id){
        $data = ['_model' => $model_type];

        switch ($model_type){
            case 'group':
                $data['model'] = Group::findById($model_id)->firstOrFail()->toArray();
                $data['_model_str'] = 'группам';
                break;
            case 'teacher':
                $data['models'] = User::query()
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', 'App\\Models\\User')
                    ->where('model_has_roles.model_id', '=', $model_id)
                    ->firstOrFail()->toArray();
                $data['_model_str'] = 'преподавателям';
                break;
            case 'auditories':
                $data['models'] = Auditory::findById($model_id)->firstOrFail()->toArray();
                $data['_model_str'] = 'аудиториям';
                break;
            default: abort(404);
        }
    }

    // <editor-fold defaultstate="collapse" desc="Current Schedule">

    public function edit_index(){
        return view('schedule.edit.index');
    }

    /**
     * Returns dates in current week number
     *
     * @param int $week
     */
    public function weekToDates(int $week){
        if ($week < 0 || $week > 29){
            return null;
        }

        $monday = new \DateTimeImmutable('monday this week'); //Tricky!

        $monday = $week == 0 ? $monday : $monday->add(new \DateInterval('P'.$week.'W'));

        $result = [];
        array_push($result, $monday);

        $i = 0;
        while ($i < 6){
            array_push($result, $result[$i]->add(new \DateInterval('P1D')));
            $i++;
        }

        return $result;
    }

    public function edit_schedule($model, $id, $week){

        if ($week < 0 || $week > 29)
            abort(404);

        $data = [];

        $data['available_weeks'] = [];


        for ($i = 0; $i < 30; $i++)
            array_push($data['available_weeks'], ['number' => $i, 'dates' => $this->weekToDates($i), 'active' => $i == $week]);

        $data['current_week'] = ['number' => $week, 'dates' => $this->weekToDates($week)];

        $data['start_date'] = $data['available_weeks'][0]['dates'][0];
        $data['end_date'] = $data['available_weeks'][count($data['available_weeks'])-1]['dates'][6];

        $days_of_week = [
            'mon',
            'tue',
            'wed',
            'thu',
            'fri',
            'sat',
            'sun'
        ];

        $data['current_schedule'] = [];



        if ($model == 'group'){
            $group = Group::query()->where('id', '=', $id)->firstOrFail();

            $data['_model'] = $model;
            $data['model'] = $group;

            for ($i = 0; $i < count($days_of_week); $i++){
                $data['current_schedule'][$days_of_week[$i]] = ScheduleSession::byDate($data['current_week']['dates'][$i])->where('group_id', '=', $group->id)->get();
            }
            $role_teacher = Role::findByName('teacher');
            $data['teachers'] = User::query()->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->where('model_has_roles.model_type', '=', 'App\\Models\\User')
                ->where('model_has_roles.role_id', '=', $role_teacher->id)
                ->select(['users.id', 'users.name'])->get()->toArray();

            $data['auditories'] = Auditory::query()->select(['id', 'name'])->get();
            $data['groups'] = [$group];

        }
        else if ($model == 'teacher'){
            $teacher = User::query()->where('id', '=', $id)->firstOrFail();

            if (!$teacher->hasRole('teacher')){
                abort(404);
            }

            $data['_model'] = $model;
            $data['model'] = $teacher;

            for ($i = 0; $i < count($days_of_week); $i++){
                $data['current_schedule'][$days_of_week[$i]] = ScheduleSession::byDate($data['current_week']['dates'][$i])->where('teacher_id', '=', $teacher->id)->get();
            }
            $data['teachers'] = [$teacher->makeHidden('roles')];

            $data['auditories'] = Auditory::query()->select(['id', 'name'])->get();
            $data['groups'] = Group::query()->select(['id', 'name'])->get();
        }
        else if ($model == 'auditory'){
            $auditory = Auditory::query()->where('id', '=', $id)->firstOrFail();

            $data['_model'] = $model;
            $data['model'] = $auditory;

            for ($i = 0; $i < count($days_of_week); $i++){
                $data['current_schedule'][$days_of_week[$i]] = ScheduleSession::byDate($data['current_week']['dates'][$i])->where('auditory_id', '=', $auditory->id)->get();
            }

            $role_teacher = Role::findByName('teacher');
            $data['teachers'] = User::query()->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->where('model_has_roles.model_type', '=', 'App\\Models\\User')
                ->where('model_has_roles.role_id', '=', $role_teacher->id)
                ->select(['users.id', 'users.name'])->get()->toArray();

            $data['auditories'] = [$auditory];
            $data['groups'] = Group::query()->select(['id', 'name'])->get();
        }
        else {
            abort(404);
        }

        $data['subjects'] = Subject::query()->select(['id', 'name'])->get();

        return view('schedule.edit.edit', $data);
    }

    public function ajax_current_schedule(Request $request){
        $validator = \Validator::make($request->input(), [
            'model' => [
                'required',
                'max:40',
                Rule::in(['group', 'teacher', 'auditory'])
            ],
            'date' => [
                'required',
                'date',
                'date_format:d.m.Y'
            ]
        ]);

        $data = $request->input();

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $model = $request->input('model');

        $validator = \Validator::make($request->input(), [
            'model_id' => [
                'required',
                'numeric',
                Rule::exists((function($model){
                    switch ($model){
                        case 'group': return 'groups';
                        case 'teacher': return 'users';
                        case 'auditory': return 'auditories';
                        default: return '';
                    }
                })($model), 'id')
            ]
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }



        if ($model == 'group'){
            $group = Group::findById($request->input('model_id'))->firstOrFail();

            return response()->json(ScheduleSession::byDate(\DateTimeImmutable::createFromFormat('d.m.Y', $request->input('date')))->where('group_id', '=', $group->id)->get());
        }
        else if ($model == 'teacher'){
            $teacher = User::query()->where('id', '=', $request->input('model_id'))->firstOrFail();

            if (!$teacher->hasRole('teacher')){
                abort(404);
            }

            return response()->json(ScheduleSession::byDate(\DateTimeImmutable::createFromFormat('d.m.Y', $request->input('date')))->where('teacher_id', '=', $teacher->id)->get());
        }
        else if ($model == 'auditory'){
            $auditory = Auditory::findById($request->input('model_id'))->firstOrFail();

            return response()->json(ScheduleSession::byDate(\DateTimeImmutable::createFromFormat('d.m.Y', $request->input('date')))->where('auditory_id', '=', $auditory->id)->get());
        }
        return response('not okay!', 400);

    }

    public function ajax_current_schedule_create(Request $request){
        $validator = \Validator::make($request->json()->all(), [
            'number' => [
                'required',
                'numeric',
                Rule::in([1, 2, 3, 4, 5, 6, 7, 8]),
            ],
            'subgroup' => [
                'required',
                'numeric',
                Rule::in([-1, 1, 2, 3, 4]),
            ],
            'group_id' => [
                'required',
                'numeric',
                Rule::exists('groups', 'id')
            ],
            'teacher_id' => [
                'required',
                'numeric',
                Rule::exists('users', 'id')
            ],
            'auditory_id' => [
                'required',
                'numeric',
                Rule::exists('auditories', 'id')
            ],
            'subject_id' => [
                'required',
                'numeric',
                Rule::exists('subjects', 'id')
            ],
            'date' => [
                'required',
                'date',
                'date_format:d.m.Y'
            ]
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $teacher = User::findById($request->json('teacher_id'))->firstOrFail();
        if (!$teacher->hasRole('teacher')){
            return response()->json(['status' => 400, 'errors' => ['teacher_id' => 'Указан неверный преподаватель!']], 400);
        }
        $date = \DateTimeImmutable::createFromFormat('d.m.Y', $request->json('date'));
        $group = Group::findById($request->json('group_id'))->firstOrFail();
        $subject = Subject::findById($request->json('subject_id'))->firstOrFail();
        $auditory = Auditory::findById($request->json('auditory_id'))->firstOrFail();
        $rings = RingsSchedule::findByGroup($group);
        $number = intval($request->json('number'));
        $subgroup = intval($request->json('subgroup'));
        if ($subgroup == -1){
            $subgroup = null;
        }
        $time = $rings[array_search($number, array_column($rings, 'session_number'))];
        //dn(t+a+g(s+se))
        $conflict = ScheduleSession::byDate($date)
            ->where('number', '=', $number)
            ->where(function ($query) use ($teacher, $auditory, $group, $subgroup){
                $query->where('teacher_id', '=', $teacher->id)
                    ->orWhere('auditory_id', '=', $auditory->id)
                    ->orWhere('group_id', '=', $group->id)
                    ->where(function ($query) use ($subgroup){
                        $query->where('subgroup', '=', $subgroup)
                            ->orWhere('subgroup', '=', null);
                    });
            })->get();

        if ($conflict->count() > 0){
            $conflict_group = Group::findById($conflict[0]->group_id)->first();
            $conflict_teacher = User::findById($conflict[0]->teacher_id)->first();
            $conflict_auditory = Auditory::findById($conflict[0]->auditory_id)->first();
            return response()->json(['status' => 409, 'errors' => "Обнаружены конфликтные записи! Конфликтная запись: \"Дата: {$conflict[0]->starts_at->format('d.m.Y')}, Пара: {$conflict[0]->number}, Группа: {$conflict_group->name}, Преподаватель: {$conflict_teacher->name}, Аудитория: {$conflict_auditory->name}\""], 409);
        }

        $ss = new ScheduleSession();
        $ss->group_id = $group->id;
        $ss->teacher_id = $teacher->id;
        $ss->subject_id = $subject->id;
        $ss->auditory_id = $auditory->id;
        $ss->number = $number;
        $ss->subgroup = $subgroup;
        $ss->starts_at = \DateTime::createFromFormat(
            'd.m.Y H:i',
            $request->json('date') . ' ' . $time['starts_at']);
        $ss->interrupts_at = \DateTime::createFromFormat(
            'd.m.Y H:i',
            $request->json('date') . ' ' . $time['interrupts_at']);
        $ss->continues_at = \DateTime::createFromFormat(
            'd.m.Y H:i',
            $request->json('date') . ' ' . $time['continues_at']);
        $ss->ends_at = \DateTime::createFromFormat(
            'd.m.Y H:i',
            $request->json('date') . ' ' . $time['ends_at']);

        $ss->save();

        return response()->json($ss, 200);
    }

    public function ajax_current_schedule_edit(Request $request){
        $validator = \Validator::make($request->json()->all(), [
            'id' => [
                'required',
                'numeric',
                Rule::exists('schedule_sessions', 'id')
            ],
            'number' => [
                'required',
                'numeric',
                Rule::in([1, 2, 3, 4, 5, 6, 7, 8]),
            ],
            'subgroup' => [
                'required',
                'numeric',
                Rule::in([-1, 1, 2, 3, 4]),
            ],
            'group_id' => [
                'required',
                'numeric',
                Rule::exists('groups', 'id')
            ],
            'teacher_id' => [
                'required',
                'numeric',
                Rule::exists('users', 'id')
            ],
            'auditory_id' => [
                'required',
                'numeric',
                Rule::exists('auditories', 'id')
            ],
            'subject_id' => [
                'required',
                'numeric',
                Rule::exists('subjects', 'id')
            ],
            'date' => [
                'required',
                'date',
                'date_format:d.m.Y'
            ],
            ],
            [
                'id.required' => 'ИД обязателен!',
                'id.numeric' => 'ИД должен быть числом!',
                'id.exists' => 'ИД не найден в базе данных!',

                'number.required' => 'Номер обязателен!',
                'number.numeric' => 'Номер должен быть числом!',
                'number.in' => 'Номер должен входить в множество чисел от 1 до 8!',

                'subgroup.required' => 'Номер подгруппы обязателен!',
                'subgroup.numeric' => 'Номер погруппы должен быть числом!',
                'subgroup.in' => 'Номер подгруппы должен входить в множество чисел от 1 до 4 и -1 при отсутствии!',

                'group_id' => 'Указана неверная группа!',
                'teacher_id' => 'Указан неверный преподаватель!',
                'auditory_id' => 'Указана неверная аудитория!',
                'subject_id' => 'Указан неверный предмет!',

                'date' => 'Указана неверная дата!'
            ]
        );

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $teacher = User::findById($request->json('teacher_id'))->firstOrFail();
        if (!$teacher->hasRole('teacher')){
            return response()->json(['status' => 400, 'errors' => ['teacher_id' => 'Указан неверный преподаватель!']], 400);
        }
        $id = intval($request->json('id'));
        $date = \DateTimeImmutable::createFromFormat('d.m.Y', $request->json('date'));
        $group = Group::findById($request->json('group_id'))->firstOrFail();
        $subject = Subject::findById($request->json('subject_id'))->firstOrFail();
        $auditory = Auditory::findById($request->json('auditory_id'))->firstOrFail();
        $rings = RingsSchedule::findByGroup($group);
        $number = intval($request->json('number'));
        $subgroup = intval($request->json('subgroup'));
        if ($subgroup == -1){
            $subgroup = null;
        }
        $time = $rings[array_search($number, array_column($rings, 'session_number'))];
        //d(-i)n(t+a+g(s+se))
        $conflict = ScheduleSession::byDate($date)
            ->where('id', '!=', $id)
            ->where('number', '=', $number)
            ->where(function ($query) use ($teacher, $auditory, $group, $subgroup){
                $query->where('teacher_id', '=', $teacher->id)
                    ->orWhere('auditory_id', '=', $auditory->id)
                    ->orWhere('group_id', '=', $group->id)
                    ->where(function ($query) use ($subgroup){
                        $query->where('subgroup', '=', $subgroup)
                            ->orWhere('subgroup', '=', null);
                    });
            })->get();



        if ($conflict->count() > 0){

            $conflict_group = Group::findById($conflict[0]->group_id)->first();
            $conflict_teacher = User::findById($conflict[0]->teacher_id)->first();
            $conflict_auditory = Auditory::findById($conflict[0]->auditory_id)->first();
            return response()->json(['status' => 409, 'errors' => "Обнаружены конфликтные записи! Конфликтная запись: \"Дата: {$conflict[0]->starts_at->format('d.m.Y')}, Пара: {$conflict[0]->number}, Группа: {$conflict_group->name}, Преподаватель: {$conflict_teacher->name}, Аудитория: {$conflict_auditory->name}\""], 409);
        }

        $ss = ScheduleSession::byId()->firstOrFail();
        $ss->group_id = $group->id;
        $ss->teacher_id = $teacher->id;
        $ss->subject_id = $subject->id;
        $ss->auditory_id = $auditory->id;
        $ss->number = $number;
        $ss->subgroup = $subgroup;
        $ss->starts_at = \DateTime::createFromFormat(
            'd.m.Y H:i',
            $request->json('date') . ' ' . $time['starts_at']);
        $ss->interrupts_at = \DateTime::createFromFormat(
            'd.m.Y H:i',
            $request->json('date') . ' ' . $time['interrupts_at']);
        $ss->continues_at = \DateTime::createFromFormat(
            'd.m.Y H:i',
            $request->json('date') . ' ' . $time['continues_at']);
        $ss->ends_at = \DateTime::createFromFormat(
            'd.m.Y H:i',
            $request->json('date') . ' ' . $time['ends_at']);

        $ss->save();

        return response()->json($ss, 200);
    }

    public function ajax_current_schedule_delete(Request $request){
        $validator = \Validator::make($request->json()->all(), [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('schedule_sessions', 'id')
                ]
            ]
        );

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        ScheduleSession::byId($request->json('id'))->delete();

        return response('ok!', 200);
    }

    public function ajax_sync_schedule(Request $request){
        $validator = \Validator::make($request->json()->all(), [
            'start_date' => [
                'date',
                'date_format:d.m.Y'
            ],
            'end_date' => [
                'date',
                'date_format:d.m.Y',
                'after:start_date'
            ],
            'group_id' => [
                'numeric',
                Rule::exists('groups', 'id')
            ]
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $start = \DateTime::createFromFormat('d.m.Y', $request->json('start_date'));
        $end = \DateTime::createFromFormat('d.m.Y', $request->json('end_date'));
        $group = Group::query()->where('id', '=', $request->json('group_id'))->first();

        //Deleting old schedule

        ScheduleSession::byDates($start, $end)->where('group_id', '=', $group->id)->delete();

        //Getting primary and rings schedule

        $primary_schedule = json_decode(PrimarySchedule::findByGroup($group)->schedule, true);

        $rings_schedule = RingsSchedule::findByGroup($group);

        $processing_date = \DateTimeImmutable::createFromMutable($start);

        $rings_format = 'd.m.Y H:i';

        //Setting new schedule

        while (intval($processing_date->diff($end)->format('%R%a')) > -1){
            $schedule = $primary_schedule[isWeekOdd($processing_date) ? 'odd' : 'even'][strtolower($processing_date->format('D'))];

            foreach ($schedule as $session){
                if ($session['subject'] == -1 || $session['teacher'] == -1 || $session['auditory'] == -1) continue;

                try {
                    $subject = Subject::query()->where('id', '=', $session['subject'])->firstOrFail();
                    $teacher = User::query()->where('id', '=', $session['teacher'])->firstOrFail();
                    $auditory = Auditory::query()->where('id', '=', $session['auditory'])->firstOrFail();
                }
                catch (ModelNotFoundException $exception){
                    continue;
                }

                $rings = $rings_schedule[array_search($session['number'], array_column($rings_schedule, 'session_number'))];

                $date = $processing_date->format('d.m.Y');

                $ss = new ScheduleSession();
                $ss->group_id = $group->id;
                $ss->subject_id = $subject->id;
                $ss->teacher_id = $teacher->id;
                $ss->auditory_id = $auditory->id;
                $ss->number = $session['number'];
                $ss->starts_at = \DateTime::createFromFormat($rings_format, $date . ' ' . $rings['starts_at']);
                $ss->interrupts_at = \DateTime::createFromFormat($rings_format, $date . ' ' . $rings['interrupts_at']);
                $ss->continues_at = \DateTime::createFromFormat($rings_format, $date . ' ' . $rings['continues_at']);
                $ss->ends_at = \DateTime::createFromFormat($rings_format, $date . ' ' . $rings['ends_at']);
                $ss->save();
            }
            $processing_date = $processing_date->add(new \DateInterval('P1D'));
        }

        return response('ok!', 200);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapse" desc="Rings Schedule">

    public function rings_edit($rings = null){
        if ($rings){
            try {
                $rings = RingsSchedule::query()->where('id', '=', $rings)->firstOrFail();
            }
            catch (\Exception $exception){
                abort(404);
            }

            $rings->rings = json_decode($rings->rings);

            return view('schedule.edit.rings.edit', ['rings' => $rings]);
        }
        else {
            return view('schedule.edit.rings.index');
        }
    }

    public function rings_ajax(){
        $rings = RingsSchedule::all();

        $rings_out = [];

        foreach ($rings as $ring){
            $groups = $ring->groups()->get();
            $groups_str = '';

            for ($i = 0; $i < count($groups)-1; $i++){
                $groups_str .= $groups[$i]->name . ', ';
            }

            if (count($groups) - 1 >= 0) {
                $groups_str .= $groups[count($groups) - 1]->name;
            }

            array_push($rings_out, [
                'id' => $ring->id,
                'rings' => json_decode($ring->rings),
                'groups' => $groups,
                'groups_str' => $groups_str
            ]);
        }

        return response()->json($rings_out);
    }

    public function rings_groups_ajax($rings){
        $rings = RingsSchedule::query()->where('id', '=', $rings)->firstOrFail();

        return response()->json($rings->groups()->get());
    }

    public function create_rings_ajax(){
        $default_rings = json_encode(config('mynmc.default_rings'), JSON_UNESCAPED_UNICODE);
        $model = new RingsSchedule();
        $model->rings = $default_rings;
        $model->saveOrFail();
        $model->groups_str = '';
        return response()->json($model);

    }

    public function delete_rings_ajax($rings){
        RingsSchedule::query()->where('id', '=', $rings)->firstOrFail()->delete();

        return response('Успешно удалено');
    }

    public function update_rings_ajax($rings, Request $request){

        $validator = \Validator::make($request->json()->all(), [
            'groups' => 'required|array',
            'groups.*.id' => 'required|numeric|exists:App\\Models\\Group,id',
            'rings' => 'required|array',
            'rings.*.session_number' => 'required|digits_between:1,8',
            'rings.*.starts_at' => 'required|date_format:H:i',
            'rings.*.interrupts_at' => 'required|date_format:H:i|after:rings.*.starts_at',
            'rings.*.continues_at' => 'required|date_format:H:i|after_or_equal:rings.*.interrupts_at',
            'rings.*.ends_at' => 'required|date_format:H:i|after:rings.*.continues_at'
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $groups = $request->json('groups');
        $schedule = $request->json('rings');

        try {
            $rings = RingsSchedule::query()->where('id', '=', $rings)->firstOrFail();
        }
        catch (\Exception $exception){
            abort(404);
        }

        $groups_to_apply = [];

        foreach ($groups as $group) {
            array_push($groups_to_apply, Group::query()->where('id', '=', $group['id'])->firstOrFail());
        }

        if (count($schedule) != 8){
            return response('Данные повреждены', 400);
        }

        $numbers_to_check = [1, 2, 3, 4, 5, 6, 7, 8];

        foreach ($schedule as $item){
            $index = array_search($item['session_number'], $numbers_to_check);

            if ($index == false && $index != 0){
                return response('Данные повреждены', 400);
            }

            array_splice($numbers_to_check, $index, 1);
        }

        if (count($numbers_to_check) != 0){
            return response('Данные повреждены', 400);
        }

        $rings->rings = json_encode($schedule, JSON_UNESCAPED_UNICODE);
        $rings->setGroups($groups_to_apply);
        $rings->saveOrFail();

        return response('ok!');
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Primary Schedule">

    public function primary_schedule_groups(){
        return view('schedule.edit.primary.index');
    }

    protected static function getTeachers(){
        $role_id = Role::findByName('teacher')->id;
        $teachers_id = DB::table('model_has_roles')
            ->where('model_type', '=', 'App\\Models\\User')
            ->where('role_id', '=', $role_id)
            ->select(['model_id']);

        return User::query()->whereIn('id', $teachers_id)->get();
    }

    public function primary_schedule($group, $week_number, $day_of_week){

        $days_of_week = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        try {
            $group = Group::query()->where('id', '=', $group)->firstOrFail();
        }
        catch (ModelNotFoundException $exception){
            abort(404);
        }

        try {
            $primary_schedule = PrimarySchedule::findByGroup($group)->getScheduleByDay($week_number, $day_of_week);
        }
        catch (ModelNotFoundException $exception){
            $primary_schedule = new PrimarySchedule();
            $primary_schedule->setSchedule(config('mynmc.default_primary_schedule'));
            $primary_schedule->saveOrFail();
            $primary_schedule->setGroup($group);
            $primary_schedule->saveOrFail();
            $primary_schedule = $primary_schedule->getScheduleByDay($week_number, $day_of_week);
        }

        $subjects = Subject::all();
        $subjects_out = [];

        foreach ($subjects as $subject){
            $subjects_out[strval($subject->id)] = $subject->name;
        }

        $teachers = self::getTeachers();
        $teachers_out = [];

        foreach ($teachers as $teacher){
            $teachers_out[strval($teacher->id)] = $teacher->name;
        }

        $auditories = Auditory::all();
        $auditories_out = [];

        foreach ($auditories as $auditory){
            $auditories_out[strval($auditory->id)] = $auditory->name;
        }

        return view('schedule.edit.primary.edit', [
            'group' => $group,
            'primary_schedule' => $primary_schedule,
            'week_number' => $week_number,
            'day_of_week' => $day_of_week,
            'days_of_week' => $days_of_week,
            'teachers' => $teachers_out,
            'subjects' => $subjects_out,
            'auditories' => $auditories_out
        ]);
    }

    public function edit_primary_schedule_ajax(Request $request, $group, $week_number, $day_of_week){
        $group = Group::query()->where('id', '=', $group)->firstOrFail();

        $validator = \Validator::make($request->json()->all(), [
            'schedule' => [
                'required',
                'array',
                'max:8',
                'min:8'
            ],
            'schedule.*.number' => 'required|numeric|max:8|min:1',
        ]);

        if ($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        //ok so laravel's validator doesn't support any logic so we need to do this РУЧКАМИ

        $schedule_to_save = [];

        $schedule = $request->json('schedule');

        $numbers_to_check = [1, 2, 3, 4, 5, 6, 7, 8];


        for ($i = 0; $i < count($schedule); $i++){
            $item = $schedule[$i];

            $index = array_search($item['number'], $numbers_to_check);

            if ($index == false && $index != 0){
                return response('Данные повреждены', 400);
            }

            array_splice($numbers_to_check, $index, 1);

            if ($item['subject'] != -1) {
                try {
                    $item['subject'] = Subject::query()->where('id', '=', $item['subject'])->firstOrFail()->id;
                } catch (ModelNotFoundException $exception) {
                    return response('Данные повреждены', 400);
                }
            }
            else {
                array_push($schedule_to_save, ['number' => intval($item['number']), 'subject' => -1, 'teacher' => -1, 'auditory' => -1]);
                continue;
            }
            if ($item['teacher'] != -1) {
                try {
                    $item['teacher'] = User::query()->where('id', '=', $item['teacher'])->firstOrFail()->id;
                } catch (ModelNotFoundException $exception) {
                    return response('Данные повреждены', 400);
                }
            }
            else {
                array_push($schedule_to_save, ['number' => intval($item['number']), 'subject' => -1, 'teacher' => -1, 'auditory' => -1]);
                continue;
            }
            if ($item['auditory'] != -1) {
                try {
                    $item['auditory'] = Auditory::query()->where('id', '=', $item['auditory'])->firstOrFail()->id;
                } catch (ModelNotFoundException $exception) {
                    return response('Данные повреждены', 400);
                }
            }
            else {
                array_push($schedule_to_save, ['number' => intval($item['number']), 'subject' => -1, 'teacher' => -1, 'auditory' => -1]);
                continue;
            }



            array_push($schedule_to_save, [
                'number' => intval($item['number']),
                'subject' => intval($item['subject']),
                'teacher' => intval($item['teacher']),
                'auditory' => intval($item['auditory'])
            ]);
        }

        if (count($numbers_to_check) > 0){
            return response('Данные повреждены', 400);
        }

        $primary_schedule = PrimarySchedule::findByGroup($group);
        $primary_schedule->setScheduleByDay($schedule_to_save, $week_number, $day_of_week);
        $primary_schedule->save();

        return response('Успешно сохранено!'); //omg it's terrible
    }

    // </editor-fold>
}
