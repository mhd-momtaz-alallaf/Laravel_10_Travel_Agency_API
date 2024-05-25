<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [ // the table that have the forignkey will be have a ->belongsTo() that model of the forignkey (the tour belongsTo the travel)
        'travel_id', // the relation between the tours and travels is one to many (one travel => many tours)
        'name',
        'starting_date',
        'ending_date',
        'price',
    ];

    public function price(): Attribute // accessor and mutator to control the price store and show.
    {
        return Attribute::make(
            get: fn ($value) => $value / 100, // when showing the price it will showing as float (and that for the cents of the dollar)
            set: fn ($value) => $value * 100  // when storing the price it well be integer in the database.
        );
    }
}
