<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RingsSchedule extends Model
{
    use HasFactory, HasRelationships;

    protected $table = "rings_schedule";

    protected $primaryKey = "id";

    public $timestamps = false;

    function groups(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(
            Group::class,
            'model',
            'model_has_groups',
            'model_id',
            'group_id'
        );
    }

    function setGroups($groups){
        $this->groups()->detach();
        $this->groups()->saveMany($groups);
    }

    public static function findByGroup($group){
        return json_decode(RingsSchedule::query()->join('model_has_groups', 'rings_schedule.id', '=', 'model_has_groups.model_id')
            ->where('model_has_groups.model_type', '=', 'App\\Models\\RingsSchedule')
            ->where('model_has_groups.group_id', '=', $group->id)->firstOrFail()->rings, true);
    }
}
