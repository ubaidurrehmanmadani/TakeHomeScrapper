<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'articles';

    public static function getDataByType($sourceType){
        return self::where('api_source_id', $sourceType)->count();
    }
}
