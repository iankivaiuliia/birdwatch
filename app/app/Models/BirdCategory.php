<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BirdCategory extends Model
{
    protected $table = 'bird_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];
}
