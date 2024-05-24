<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
    use HasFactory, Sluggable; // The Cviebrock\Sluggable Package is for making the 'slug' field a unique field in the table. 

    protected $table = "travels"; // because the travel model name is irregular word and doesn't accept the plural form, so we manually add the 's' in the table name.

    protected $fillable = [
        'is_public',
        'slug',
        'name',
        'description',
        'number_of_days',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name', // specifying the source of the slug, to get the 'slug' from the 'name' field and it will be a unique slug.
            ]
        ];
    }

    public function numperOfNights(): Attribute // this is the new laravel 10 syntax of the Accessor, and this accessor is to auto compute the number of nights from 'number_of_days' field.
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['number_of_days'] - 1
        );
    }
}
