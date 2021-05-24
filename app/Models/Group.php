<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory, HasRelationships;

    protected $table = "groups";

    protected $primaryKey = "id";

    public $timestamps = false;

    public function users(){
        return $this->relatedModels(User::class);
    }

    public function ringsSchedule(){
        return $this->relatedModels(RingsSchedule::class);
    }

    public function primarySchedule(){
        return $this->relatedModels(PrimarySchedule::class);
    }

    public function relatedModels($model){
        return $this->morphedByMany(
            $model,
            'model',
            'model_has_groups',
            'group_id',
            'model_id'
        );
    }

    public function setUsers($users){
        $this->users()->detach();
        $this->users()->saveMany($users);
    }

    public function setRingsSchedule($rings){
        $this->users()->detach();
        $this->users()->save($rings);
    }

    public function assignUser(User $user){
        $this->users()->save($user);
    }


}
