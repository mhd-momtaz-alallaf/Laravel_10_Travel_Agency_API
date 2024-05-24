<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [ // the table that have the forignkey will be have a ->belongsTo() that model of the forignkey (the tour belongsTo the travel)
        'travel_id', // the relation between the tours and travels is one to many (one travel => many tours)
        'name',
        'starting_date',
        'ending_date',
        'price',
    ];
}
