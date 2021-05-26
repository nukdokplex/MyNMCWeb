<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PrimarySchedule extends Model
{
    use HasFactory, HasRelationships;

    protected $table = "primary_schedule";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function group(){
        return $this->morphToMany(
            Group::class,
            'model',
            'model_has_groups',
            'model_id',
            'group_id'
        )->firstOrFail();
    }

    public function getScheduleByDay($week_number, $day){
        //$ASDASD = $this->schedule;

        return $this->getSchedule()[$week_number][$day];
    }

    public static function findByGroup($group){
        try {
            return $group->primarySchedule()->firstOrFail();
        }
        catch (ModelNotFoundException $exception){
            $ps = new PrimarySchedule();
            $ps->setSchedule(config('mynmc.default_primary_schedule'));
            $ps->save();
            $ps->setGroup($group);
            $ps->save();
            return $ps;
        }
    }

    public function getSchedule(){
        return json_decode($this->schedule, true);
    }
    public function setGroup($group){
        $relationship = $this->morphToMany(
            Group::class,
            'model',
            'model_has_groups',
            'model_id',
            'group_id'
        );
        $relationship->detach();
        $relationship->save($group);
    }

    public function setScheduleByDay($schedule, $week_day, $day){
        $schedule_to_apply = $this->getSchedule();

        $schedule_to_apply[$week_day][$day] = $schedule;

        $this->setSchedule($schedule_to_apply);
    }

    public function setSchedule($schedule){
        $this->schedule = json_encode($schedule, JSON_UNESCAPED_UNICODE);
    }
}
