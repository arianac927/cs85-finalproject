<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'name',
        'type_line',
        'color_identity',
        'commander_legal',
    ];

    protected $casts = [
        'color_identity' => 'array',
        'commander_legal'=> 'boolean',
    ];
}
