<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class ScheduleSession extends Model
{
    use HasFactory;

    protected $table = "schedule_sessions";

    protected $primaryKey = "id";

    public $timestamps = true;

    protected $casts = [
        'starts_at' => 'datetime',
        'interrupts_at' => 'datetime',
        'continues_at' => 'datetime',
        'ends_at' => 'datetime'
    ];

    public function group(){
        return Group::query()->where('id', '=', $this->group_id)->first();
    }

    public function teacher(){
        $role = Role::findByName('teacher');

        return User::query()->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.model_type', '=', 'App\\Models\\User')
            ->where('model_has_roles.role_id', '=', $role->id)
            ->where('users.id', '=', $this->teacher_id)->first();
    }

    public function subject(){
        return Subject::query()->where('id', '=', $this->subject_id)->first();
    }

    public function auditory(){
        return Auditory::query()->where('id', '=', $this->auditory_id)->first();
    }

    public static function byWeek(array $week){
        $dates = [$week[0], $week[6]];

        return ScheduleSession::query()->whereBetween('starts_at', $dates)->orderBy('number', 'asc');
    }

    public static function byDate(\DateTimeInterface $date){
        $start = \DateTimeImmutable::createFromFormat('d.m.Y H:i:s', $date->format('d.m.Y') . ' 00:00:00');
        $end = \DateTimeImmutable::createFromFormat('d.m.Y H:i:s', $date->format('d.m.Y') . ' 23:59:59');

        return static::byDates($start, $end);
    }

    public static function byDates(\DateTimeInterface $start, \DateTimeInterface $end){
        return ScheduleSession::query()->whereBetween('starts_at', [$start, $end])->orderBy('number', 'asc');
    }

    public static function byId($id){
        return static::query()->where('id', '=', $id);
    }
}
