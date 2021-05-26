<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory, HasRelationships;

    protected $table = "subjects";

    protected $primaryKey = "id";

    public $timestamps = false;

    public static function findById($id){
        return static::query()->where('id', '=', $id);
    }
}
