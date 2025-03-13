<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimpsonsQuote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'quote',
        'character',
        'image',
        'characterDirection',
    ];
}
