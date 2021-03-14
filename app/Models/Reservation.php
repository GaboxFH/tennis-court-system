<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public $table = 'reservations';
    protected $fillable = [
        'title', 'date', 'court', 'user_id', 'startTime', 'duration'
    ];

    /*
    public function user() {
        return $this->belongsTo(User::class, 'created-by', 'id');
    }
    */

    public function users() {
        return $this->belongsToMany(User::class);
    }    
}
