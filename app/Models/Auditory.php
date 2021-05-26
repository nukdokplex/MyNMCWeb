<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditory extends Model
{
    use HasFactory;

    protected $table = "auditories";

    protected $primaryKey = "id";

    public $timestamps = false;

    public static function findById($id){
        return static::query()->where('id', '=', $id);
    }
}
