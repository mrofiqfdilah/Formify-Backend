<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class forms extends Model
{
    use HasFactory;
    protected $table = 'forms';
    protected $guarded = ['id'];

    public $timestamps = false;
}
