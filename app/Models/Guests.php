<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guests extends Model
{
    use HasFactory;

    public $table = 'guests';
    protected $fillable = [
        'id',
        'name',
        'user_id',
        'reservation_id'
    ];

}
