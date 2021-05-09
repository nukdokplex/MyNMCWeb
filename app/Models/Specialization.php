<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory, HasRelationships;

    protected $table = 'specializations';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public function groups(){
        return $this->belongsToMany(
            Group::class,
            'group_has_specialization',
            'specialization_id',
            'group_id'
        );
    }

    public function setGroups($groups){
        $this->groups()->detach();
        $this->groups()->saveMany($groups);
    }
}
