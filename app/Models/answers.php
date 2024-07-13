<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class answers extends Model
{
    use HasFactory;
    protected $table = 'answers';
    protected $guarded = ['id'];

    public $timestamps = false;

  

    public function questions()
    {
        return $this->belongsTo(Questions::class,'question_id');
    }
}
