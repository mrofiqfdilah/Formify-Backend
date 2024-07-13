<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class allowed_domain extends Model
{
    use HasFactory;
    protected $table = 'allowed_domains';
    protected $guarded = ['id'];

    public $timestamps = false;
}
