<?php

namespace App\Http\Controllers;

use App\Models\Auditory;
use App\Models\Group;
use App\Models\PrimarySchedule;
use App\Models\RingsSchedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ScheduleController extends Controller
{
    public function index(){

    }

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
