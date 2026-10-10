<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'education';

    protected $fillable = ['degree', 'school', 'country', 'from_year', 'to_year', 'cgpa', 'sort_order'];
}
