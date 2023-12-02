<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APISource extends Model
{
    use HasFactory;
    protected $table = 'api_sources';
    protected $guarded = [];
}
