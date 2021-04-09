<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public $table = 'reservations';
    protected $fillable = [
        'name',
        'method', 
        'start', 
        'end', 
        'duration',
        'category', 
        'num_of_members', 
        'num_of_guests', 
        'host_id',
        'reoccur_id',
        'timed',
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
