<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    public $table = 'actions';
    protected $fillable = [
        'reservation_id', 
        'user_id', 
        'type', 
        'curr_start_datetime', 
        'curr_end_datetime', 
        'curr_court', 
        'prev_start_datetime', 
        'prev_end_datetime', 
        'prev_court', 
        'new_user_id', 
        'deleted_user_id'
    ];
}
