<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPostRedirect extends Model
{
    protected $fillable = ['from_slug', 'to_url'];
}
